<?php

namespace App\Http\Controllers;

use App\Models\TRXType;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function getBank() {
        $data = TRXType::where('fc_trx', 'BANKNAME')->get();
        return response()->json($data);
    }

    public function getTypePajak() {
        $data = TRXType::where('fc_trx', 'TAX_TYPE')->get();
        return response()->json($data);
    }

    public function getTypePPH() {
        $data = TRXType::where('fc_trx', 'PPH_TYPE')->get();
        return response()->json($data);
    }

    public function getAllWarehouse() {
        $data = Warehouse::get();
        return response()->json($data);
    }
}
