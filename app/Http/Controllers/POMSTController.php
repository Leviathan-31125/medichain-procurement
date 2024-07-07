<?php

namespace App\Http\Controllers;

use App\Models\POMST;
use Illuminate\Http\Request;

class POMSTController extends Controller
{
    public function getAllPOMST () {
        $TempPOMST = POMST::with(['podtl', 'podtl.stock','supplier'])->get();

        return response()->json(['status' => 200, 'data' => $TempPOMST]);
    }
}
