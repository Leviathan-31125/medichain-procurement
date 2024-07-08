<?php

namespace App\Http\Controllers;

use App\Models\POMST;
use Illuminate\Http\Request;

class POMSTController extends Controller
{
    public function getAllPOMST () {
        $TempPOMST = POMST::with(['supplier'])->get();

        return response()->json(['status' => 200, 'data' => $TempPOMST]);
    }

    public function getDetailPOMST($fc_pono) {
        $pono = base64_decode($fc_pono);
        $data = POMST::with(['podtl','podtl.stock', 'romst', 'supplier'])->find($pono);
        
        return response()->json($data);
    }
}
