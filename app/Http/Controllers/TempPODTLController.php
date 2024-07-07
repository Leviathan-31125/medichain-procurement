<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\TempPODTL;
use App\Models\TempPOMST;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TempPODTLController extends Controller
{
    public function getAllTempPODTLbyPONO ($fc_pono) {
        $pono_decoded = $this->DECODE_KEY($fc_pono);

        $TempPOMST = TempPOMST::find($pono_decoded);
        if(!$TempPOMST)
            return response()->json(['status' => 400, 'message' => "Not Found! Purchase Order tidak ditemukan"]);

        $TempPODTL = TempPODTL::where('fc_pono', $pono_decoded)->get();
        return response()->json(['status' => 200, 'data' => $TempPODTL]);
    }

    public function addTempPODTL (Request $request, $fc_pono) {
        $validator = Validator::make($request->all(), [
            'fc_barcode' => 'required',
            'fc_statusbonus' => 'required',
            'fm_price' => 'required',
            'fm_discprice' => 'required',
            'fn_qty' => 'required|integer|min:1',
        ], [
            'fc_barcode.required' => 'Kode Barang harus diisi',
            'fn_qty.required' => 'Kuantitas harus diisi',
            'fc_statusbonus.required' => 'Status Bonus atau Reguler tidak diketahui',
            'fm_price.required' => 'Tentukan harga sebelum melakukan Purchase Order',
            'fm_discprice.required' => 'Harga diskon harus disertakan, paling tidak 0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 300,
                'message' => $validator->errors()->first()
            ], 400);
        }
        
        $pono_decoded = $this->DECODE_KEY($fc_pono);
        $TempPOMST = TempPOMST::find($pono_decoded);
        if(!$TempPOMST)
            return response()->json(['status' => 400, 'message' => "Not Found! Purchase Order tidak ditemukan"]);

        $check_stock = Stock::find($request->fc_barcode);
        if(!$check_stock) 
            return response()->json(['status' => 400, 'message' => 'Not Found! Stock tidak tersedia pada system'], 400);

        $check_temppodtl = TempPODTL::where([
            'fc_pono' => $pono_decoded,
            'fc_barcode' => $request->fc_barcode,
            'fc_statusbonus' => $request->fc_statusbonus
        ])->first();
        if($check_temppodtl) 
            return response()->json(['status' => 400, 'message' => 'Duplicate Data! Stock sudah tersedia pada Purchase Order'], 400);

        $request->fm_discprice == null? $request->merge(['fm_discprice' => 0]) : $request->fm_discprice;
    
        $addTempPODTL = TempPODTL::create([
            'fc_pono' => $pono_decoded,
            'fc_barcode' => $request->fc_barcode,
            'fc_statusbonus' => $request->fc_statusbonus,
            'fm_price' => $request->fm_price,
            'fm_discprice' => $request->fm_discprice,
            'fn_qty' => $request->fn_qty,
            'ft_description' => $request->ft_description
        ]);

        if($addTempPODTL)
            return response()->json(['status' => 201, 'message' => 'Stock berhasil ditambahkan'], 200);
        return response()->json(['status' => 400, 'message' => 'Stock Gagal ditambahkan'], 400);
    }

    public function removeTempPODTL (Request $request, $fc_pono) {
        $pono_decoded = $this->DECODE_KEY($fc_pono);
        if($request->fn_rownum == null || !TempPODTL::where([ 'fc_pono' => $pono_decoded, 'fn_rownum' => $request->fn_rownum])->first()) 
            return response()->json(['status' => 400, 'message' => 'Detail stock tidak valid']);

        $removeTempPODTL = TempPODTL::where([
            'fc_pono' => $pono_decoded,
            'fn_rownum' => $request->fn_rownum
        ])->delete();

        if($removeTempPODTL) 
            return response()->json(['status' => 201, 'message' => 'Stock berhasil diremove']);

        return response()->json(['status' => 400, 'message' => 'Stock Gagal dihapus']);
    }

    public function getAllStock() {
        $data = Stock::with('brand')->get();
        return response()->json($data);
    }

    private function DECODE_KEY($key) {
        $result = base64_decode($key);
        return $result;
    }
}
