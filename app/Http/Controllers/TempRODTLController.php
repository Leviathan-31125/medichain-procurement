<?php

namespace App\Http\Controllers;

use App\Models\PODTL;
use App\Models\TempRODTL;
use App\Models\TempROMST;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TempRODTLController extends Controller
{
    public function getAllTempRODTLbyRONO ($fc_rono) {
        $rono_decoded = $this->DECODE_KEY($fc_rono);
        $TempROMST = TempROMST::find($rono_decoded);
        if(!$TempROMST)
            return response()->json(['status' => 400, 'message' => "Invalid Data! Data Receiving Order tidak ditemukan"], 400);

        $TempRODTL = TempRODTL::where('fc_rono', $rono_decoded)->get();
        return response()->json(['status' => 200, 'data' => $TempRODTL]);
    }

    public function addTempRODTL(Request $request, $fc_rono) {
        $validator = Validator::make($request->all(), [
            'fc_barcode' => 'required',
            'fc_statusbonus' => 'required',
            'fm_price' => 'required',
            'fm_discprice' => 'required',
            'fn_qty' => 'required|integer|min:1',
            'fc_batch' => 'required',
            'fd_expired' => 'required'
        ], [
            'fm_price.required' => 'Masukkan harga beli dari barang',
            'fc_statusbonus.required' => 'Status bonus atau reguler tidak diketahui',
            'fn_qty.required' => 'Kuantitas barang diterima harus diisi',
            'fc_barcode.required' => 'Kode barang harus diisi',
            'fc_batch.required' => 'Kode Batch Produksi wajib diisi',
            'fd_expired.required' => 'Keterangan expired wajib diisi' 
        ]);
        
        if ($validator->fails()){
            return response()->json([
                'status' => 300,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $rono_decoded = $this->DECODE_KEY($fc_rono);
        $TempROMST = TempROMST::find($rono_decoded);
        if(!$TempROMST)
            return response()->json(['status' => 400, 'message' => "Invalid Data! Data Receiving Order tidak ditemukan"], 400);

        $checkPODTL = PODTL::where([
            'fc_pono' => $TempROMST->fc_pono,
            'fc_barcode' => $request->fc_barcode
        ])->first();

        if(!$checkPODTL)
            return response()->json(['status' => 400, 'message' => 'Invalid Data! Maaf kode barang tidak ada dalam pesanan'], 400);

        if($checkPODTL->fn_qty < ($request->fn_qty + $checkPODTL->fn_qty_ro))
            return response()->json(['status' => 400, 'message' => 'Over Stock! Jumlah stock melebihi pesanan'], 400);

        $request->fm_discprice == null? $request->merge(['fm_discprice' => 0]) : $request->fm_discprice;

        $addTempRODTL = TempRODTL::create([
            'fc_rono' => $rono_decoded,
            'fc_barcode' => $request->fc_barcode,
            'fc_batch' => $request->fc_batch,
            'fd_expired' => $request->fd_expired,
            'fc_statusbonus' => $request->fc_statusbonus,
            'fm_price' => $request->fm_price,
            'fm_discprice' => $request->fm_discprice,
            'fn_qty' => $request->fn_qty,
            'ft_description' => $request->ft_description
        ]);

        if($addTempRODTL)
            return response()->json(['status' => 201, 'message' => 'Stock berhasil ditambahkan']);
        return response()->json(['status' => 400, 'message' => 'Stock Gagal ditambahkan']);
    }

    public function removeTempRODTL (Request $request, $fc_rono) {
        $rono_decoded = $this->DECODE_KEY($fc_rono);
        if($request->fn_rownum == null || !TempRODTL::where([ 'fc_rono' => $rono_decoded, 'fn_rownum' => $request->fn_rownum])->first()) 
            return response()->json(['status' => 400, 'message' => 'Detail stock tidak valid']);
    
        $removeTempRODTL = TempRODTL::where([
            'fc_rono' => $rono_decoded,
            'fn_rownum' => $request->fn_rownum
        ])->delete();
        
        if($removeTempRODTL) 
            return response()->json(['status' => 201, 'message' => 'Stock berhasil diremove']);

        return response()->json(['status' => 400, 'message' => 'Stock Gagal dihapus']);
    }

    public function updateTempRODTL (Request $request, $fc_rono) {
        $validator = Validator::make($request->all(), [
            'fn_rownum' => 'required',
            'fc_batch' => 'required',
            'fd_expired' => 'required',
            'fn_qty' => 'required',
            'fm_price' => 'required',
            'fm_discprice' => 'required'
        ], [
            'fn_rownum.required' => 'Data yang mana yang mau diupdate?',
            'fm_price.required' => 'Masukkan harga beli dari barang',
            'fn_qty.required' => 'Kuantitas barang diterima harus diisi',
            'fc_batch.required' => 'Kode Batch Produksi wajib diisi',
            'fd_expired.required' => 'Keterangan expired wajib diisi',
            'fm_discprice.required' => 'Diskon wajib dicantumkan sekalipun 0'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 300,
                'message' => $validator->errors()->first()
            ]);
        }
        
        $rono_decoded = $this->DECODE_KEY($fc_rono);
        $TempRODTL = TempRODTL::with('tempromst')->where([ 'fc_rono' => $rono_decoded, 'fn_rownum' => $request->fn_rownum])->first();
        if($request->fn_rownum == null || !$TempRODTL) 
            return response()->json(['status' => 400, 'message' => 'Detail stock tidak valid']);

        $checkPODTL = PODTL::where([
            'fc_pono' => $TempRODTL->tempromst->fc_pono,
            'fc_barcode' => $TempRODTL->fc_barcode
        ])->first();
        
        if($checkPODTL->fn_qty < ($checkPODTL->fn_qty_ro + $request->fn_qty - $TempRODTL->fn_qty))
            return response()->json(['status' => 400, 'message' => 'Over Stock! Jumlah stock melebihi pesanan']);

        $updated = $TempRODTL->update([
            'fc_batch' => $request->fc_batch,
            'fd_expired' => $request->fd_expired,
            'fn_qty' => $request->fn_qty,
            'fm_price' => $request->fm_price,
            'fm_discprice' => $request->fm_discprice,
            'ft_description' => $request->ft_description
        ]);

        if ($updated)
            return response()->json(['status' => 201, 'message' => "Atribut Stock berhasil diupdate"]);

        return response()->json(['status' => 400, 'message' => "Update Fail! Maaf, gagal mengupdate atribut stock"]);
    }

    private function DECODE_KEY($key) {
        $result = base64_decode($key);
        return $result;
    }
}
