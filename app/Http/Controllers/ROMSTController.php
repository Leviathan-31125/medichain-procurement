<?php

namespace App\Http\Controllers;

use App\Models\ROMST;
use Illuminate\Http\Request;

class ROMSTController extends Controller
{
    public function getAllROMST () {
        $TempPOMST = ROMST::with(['rodtl', 'rodtl.stock','pomst', 'pomst.supplier'])->get();

        return response()->json(['status' => 200, 'data' => $TempPOMST]);
    }
}
