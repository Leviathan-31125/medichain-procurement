<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\TempPODTL;
use App\Models\TempPOMST;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TempPOMSTController extends Controller
{
    public function getAllTempPOMST () {
        $TempPOMST = TempPOMST::with('temppodtl')->get();

        return response()->json(['status' => 200, 'data' => $TempPOMST]);
    }

    public function getDetailTempPOMST ($fc_pono) {
        $pono_decoded = $this->DECODE_KEY($fc_pono);
        $TempPOMST = TempPOMST::find($pono_decoded);

        if($TempPOMST)
            return response()->json(['status' => 200, 'data' => $TempPOMST]);
        return response()->json(['status' => 400, 'message' => "Not Found! Purchase Order aktif tidak ditemukan"]);
    }

    public function createTempPOMST (Request $request) {
        $validator = Validator::make($request->all(), [
            'fc_pono' => 'required',
            'fc_suppliercode' => 'required'
        ], [
            'fc_pono.required' => 'No. Purchase Order wajib diisi untuk diproses',
            'fc_suppliercode.required' => 'Supplier harus diisi untuk melakukan Purchase Order'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 300,
                'message' => $validator->errors()->first()
            ]);
        }

        $TempPOMST = TempPOMST::find($request->fc_pono);
        if ($TempPOMST)
            return response()->json(['data' => 400, 'message' => "Duplicate Data! User yang sama sedang membuat Sales Order"]);

        $supplier = Supplier::find($request->fc_suppliercode);
        if(!$supplier)
            return response()->json(['data' => 400, 'message' => "Invalid Data! Supplier yang belum tersedia pada system"]);

        $created = TempPOMST::create([
            'fc_pono' => $request->fc_pono,
            'fc_suppliercode' => $request->fc_suppliercode,
        ]);

        if($created) 
            return response()->json(['status' => 201, 'message' => 'Purchase Order berhasil dibuat']);

        return response()->json(['status' => 400, 'message' => 'Create Fail! Maaf Purchase Order gagal dibuat']);
    }

    public function setDetailTempPOMST (Request $request, $fc_pono) {
        $validator = Validator::make($request->all(), [
            'fd_poexpired' => 'required',
            'fc_potransport' => 'required',
            'fv_destination' => 'required',
            'fd_podate_user' => 'required',
        ], [
            'fd_poexpired.required' => 'Masukkan masa berlaku Purchase Order',
            'fc_potransport.required' => 'Media transportasi Purchase Order kosong',
            'fv_destination.required' => 'Alamat penerimaan barang tidak boleh kosong',
            'fd_podate_user.required' => 'Tanggal PO harus disertakan'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 300,
                'message' => $validator->errors()->first()
            ]);
        }
        
        $pono_decoded = $this->DECODE_KEY($fc_pono);
        $TempPOMST = TempPOMST::find($pono_decoded);
        if (!$TempPOMST)
            return response()->json(['status' => 400, 'message' => "Not Found! Purchase Order aktif tidak ditemukan"]);

        $request->fm_downpayment == null ? $request->merge(['fm_downpayment' => 0]) : '';
        $request->ft_description == null ? $request->merge(['ft_description' => '']) : '';
        
        $updated = $TempPOMST->update($request->all());
        if ($updated)
            return response()->json(['status' => 201, 'message' => "Atribut Purchase Order berhasil dilengkapi"]);
        
        return response()->json(['status' => 400, 'message' => "Updated Fail! Gagal mengupdate Atribut Purchase Order"]);
    }

    public function submitTempPOMST ($fc_pono){
        $pono_decoded = $this->DECODE_KEY($fc_pono);
        $TempPOMST = TempPOMST::find($pono_decoded);
        if (!$TempPOMST)
            return response()->json(['status' => 400, 'message' => "Not Found! Purchase Order aktif tidak ditemukan"]);

        DB::beginTransaction();

        try {
            $TempPOMST->fc_status = "SUBMIT";
            $TempPOMST->fd_podate_system = Carbon::now();
            $TempPOMST->save();

            $deletedTempPODTL = TempPODTL::where('fc_pono', $pono_decoded)->delete();
            $deletedTempPOMST = TempPOMST::where('fc_pono', $pono_decoded)->delete();

            DB::commit();

            if($deletedTempPODTL && $deletedTempPOMST)
                return response()->json(['status' => 201, 'message' => 'Purchase Order berhasil disubmit']);
        } catch (Exception $err) {
            DB::rollback();
            return response()->json([
                'status' => 400,
                'message' => 'Create Failed! Purchase Order gagal dibuat'.$err->getMessage()
            ]);
        }
    }

    private function DECODE_KEY($key) {
        $result = base64_decode($key);
        return $result;
    }   
}
