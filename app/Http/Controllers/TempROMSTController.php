<?php

namespace App\Http\Controllers;

use App\Models\POMST;
use App\Models\TempPOMST;
use App\Models\TempRODTL;
use App\Models\TempROMST;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TempROMSTController extends Controller
{
    public function getAllTempROMST () {
        $TempRoMST = TempROMST::with('temprodtl')->get();

        return response()->json(['status' => 200, 'data' => $TempRoMST]);
    }

    public function getDetailTempROMST ($fc_rono) {
        $rono_decoded = $this->DECODE_KEY($fc_rono);
        
        $TempRoMST = TempROMST::find($rono_decoded);
        if(!$TempRoMST)
            return response()->json(['status' => 400, 'message' => "Not Found! Purchase Order aktif tidak ditemukan"]);
        return response()->json(['status' => 200, 'data' => $TempRoMST]);
    }

    public function createTempROMST (Request $request) {
        $validator = Validator::make($request->all(), [
            'fc_pono' => 'required',
            'fc_sjno' => 'required',
            'fc_rono' => 'required',
            'fc_warehousecode' => 'required'
        ], [
            'fc_pono.required' => 'No. PO wajib dilampirkan',
            'fc_sjno.required' => 'No. Surat Jalan wajib dilampirkan',
            'fc_rono.required' => 'Masukkan No. Receiving Order agar diproses',
            'fc_warehousecode.required' => 'Gudang wajib dicantumkan untuk proses Receivinf Order' 
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 300,
                'message' => $validator->errors()->first()
            ]);
        }

        $POMST = POMST::find($request->fc_pono);
        if(!$POMST)
            return response()->json(['status' => 400, 'message' => 'Invalid Data! Purchase Order tidak tersedia pada system']);
        
        $warehouse = Warehouse::find($request->fc_warehousecode);
        if(!$warehouse)
            return response()->json(['status' => 400, 'message' => "Invalid Data! Data gudang tidak valid"]);

        $created = TempROMST::create([
            'fc_rono' => $request->fc_rono,
            'fc_sjno' => $request->fc_sjno,
            'fc_warehousecode' => $request->fc_warehousecode,
            'fc_pono' => $request->fc_pono
        ]);

        if($created)
            return response()->json(['status' => 201, 'message' => "Receiving Order berhasil dibuat"]);
        return response()->json(['status' => 400, 'message' => 'Create Fail! Maaf Receiving Order gagal dibuat']);
    }

    public function setDetailTempROMST (Request $request, $fc_rono) {
        $validator = Validator::make($request->all(), [
            'fd_roarrivaldate' => 'required',
            'fv_receiver' => 'required',
            'fv_arrivaladdress' => 'required'
        ], [
            'fd_roarrivaldate.required' => 'Tanggal kedatangan wajib diisi',
            'fv_receiver.required' => 'Penerima barang wajib dicantumkan',
            'fv_arrivaladdress' => 'Alamat penerimaan wajib dicantumkan'
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 300, 'message' => $validator->errors()->first()]);
        }

        $rono_decoded = $this->DECODE_KEY($fc_rono);
        $TempRoMST = TempROMST::find($rono_decoded);
        if(!$TempRoMST)
            return response()->json(['status' => 400, 'message' => "Invalid Data! Data Receiving Order tidak ditemukan"]);

        $request->ft_description == null ? $request->merge(['ft_description' => '']) : '';

        $updated = $TempRoMST->update($request->all());
        if ($updated)
            return response()->json(['status' => 201, 'message' => "Atribut Receiving Order berhasil dilengkapi"]);
        
        return response()->json(['status' => 400, 'message' => "Updated Fail! Gagal mengupdate Atribut Receiving Order"]);
    }

    public function submitTempROMST($fc_rono) {
        $rono_decoded = $this->DECODE_KEY($fc_rono);
        $TempRoMST = TempROMST::find($rono_decoded);
        if(!$TempRoMST)
            return response()->json(['status' => 400, 'message' => "Invalid Data! Data Receiving Order tidak ditemukan"]);

        DB::beginTransaction();

        try {
            $TempRoMST->fc_status = "SUBMIT";
            $TempRoMST->save();

            $deletedTempRODTL = TempRODTL::where('fc_rono', $rono_decoded)->delete();
            $deletedTempROMST = TempROMST::where('fc_rono', $rono_decoded)->delete();

            DB::commit();
            if ($deletedTempRODTL && $deletedTempROMST)
                return response()->json(['status' => 201, 'message' => 'Receiving Order berhasil disubmit']);
        } catch (Exception $err) {
            DB::rollback();
            return response()->json([
                'status' => 400,
                'message' => 'Create Failed! Receiving Order gagal dibuat'.$err->getMessage()
            ]);
        }
    }

    private function DECODE_KEY($key) {
        $result = base64_decode($key);
        return $result;
    }
}
