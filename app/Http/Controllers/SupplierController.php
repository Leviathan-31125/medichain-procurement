<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function getAllSupplier() {
        $data = Supplier::with('bank')->get();
        return response()->json(['status' => 200, 'data' => $data], 200);
    }

    public function getDetailSupplier($fc_suppliercode) {
        $suppliercode = base64_decode($fc_suppliercode);
        $data = Supplier::find($suppliercode);

        return response()->json(['status' => 200, 'data' => $data], 200);
    }

    public function createSupplier(Request $request) {
        $validator = Validator::make($request->all(), [
            'fv_suppliernpwp' => 'required',
            'fv_suppliername' => 'required',
            'fv_suppliername_alias' => 'required',
            'fv_supplieraddress' => 'required',
            'fc_picname1' => 'required',
            'fv_supplierphone1' => 'required',
            'fc_legalstatus' => 'required',
            'fc_branchtype' => 'required',
            'fc_suppliertaxcode' => 'required',
            'fv_npwpname' => 'required',
            'fv_npwpaddress' => 'required',
            'fn_agingpayable' => 'required',
            'fm_accountpayable' => 'required',
            'fc_supplierbank' => 'required',
            'fc_bankaccount' => 'required'
        ], [
            'fv_suppliernpwp.required' => 'NPWP Supplier wajib diiisi!',
            'fv_suppliername.required' => 'Nama Supplier wajib diisi!',
            'fv_suppliername_alias.required' => 'Nama alias supplier wajib diisi!',
            'fv_supplieraddress.required' => 'Alamat supplier wajib diisi!',
            'fc_picname1.required' => 'Penanggung jawab supplier wajib diisi!',
            'fv_supplierphone1.required' => 'Kontak supplier wajib diisi!',
            'fc_legalstatus.required' => 'Status legalitas supplier wajib diisi!',
            'fc_branchtype.required' => 'Status kantor supplier wajib diisi!',
            'fc_suppliertaxcode.required' => 'Jenis pajak supplier wajib diisi!',
            'fv_npwpname.required' => 'Nama NPWP supplier wajib diisi!',
            'fv_npwpaddress.required' => 'Alamat NPWP supplier wajib diisi!',
            'fn_agingpayable.required' => 'Batas hutang supplier wajib diisi!',
            'fm_accountpayable.required' => 'Max nominal hutang supplier wajib diisi!',
            'fc_supplierbank.required' => 'Bank supplier wajib diisi!',
            'fc_bankaccount.required' => 'No. Rekening supplier wajib diisi!'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 300,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $create = Supplier::create($request->all());
        if($create)
            return response()->json(['status' => 201, 'message' => 'Berhasil menambahkan supplier'], 201);

        return response()->json(['status' => 400, 'message' => 'Create Fail! Gagal menambahkan supplier'], 400);
    }

    public function updateSupplier(Request $request, $fc_suppliercode) {
        $validator = Validator::make($request->all(), [
            'fv_suppliernpwp' => 'required',
            'fv_suppliername' => 'required',
            'fv_suppliername_alias' => 'required',
            'fv_supplieraddress' => 'required',
            'fc_picname1' => 'required',
            'fv_supplierphone1' => 'required',
            'fc_legalstatus' => 'required',
            'fc_branchtype' => 'required',
            'fc_suppliertaxcode' => 'required',
            'fv_npwpname' => 'required',
            'fv_npwpaddress' => 'required',
            'fn_agingpayable' => 'required',
            'fm_accountpayable' => 'required',
            'fc_supplierbank' => 'required',
            'fc_bankaccount' => 'required'
        ], [
            'fv_suppliernpwp.required' => 'NPWP Supplier wajib diiisi!',
            'fv_suppliername.required' => 'Nama Supplier wajib diisi!',
            'fv_suppliername_alias.required' => 'Nama alias supplier wajib diisi!',
            'fv_supplieraddress.required' => 'Alamat supplier wajib diisi!',
            'fc_picname1.required' => 'Penanggung jawab supplier wajib diisi!',
            'fv_supplierphone1.required' => 'Kontak supplier wajib diisi!',
            'fc_legalstatus.required' => 'Status legalitas supplier wajib diisi!',
            'fc_branchtype.required' => 'Status kantor supplier wajib diisi!',
            'fc_suppliertaxcode.required' => 'Jenis pajak supplier wajib diisi!',
            'fv_npwpname.required' => 'Nama NPWP supplier wajib diisi!',
            'fv_npwpaddress.required' => 'Alamat NPWP supplier wajib diisi!',
            'fn_agingpayable.required' => 'Batas hutang supplier wajib diisi!',
            'fm_accountpayable.required' => 'Max nominal hutang supplier wajib diisi!',
            'fc_supplierbank.required' => 'Bank supplier wajib diisi!',
            'fc_bankaccount.required' => 'No. Rekening supplier wajib diisi!'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 300,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $suppliercode = base64_decode($fc_suppliercode);

        $supplier = Supplier::find($suppliercode);
        if(!$supplier)
            return response()->json(['status' => 400, 'message' => 'Not Found! Supplier tidak ditemukan pada system'], 400);

        $update = $supplier->update($request->except('fv_suppliernpwp'));

        if ($update)
            return response()->json(['status' => 201, 'message' => 'Berhasil emngupdate supplier'], 201);
        return response()->json(['status' => 400, 'message' => 'Update Fail! Gagal mengupdate supplier'], 400);
    }

    public function deleteSupplier ($fc_suppliercode) {
        $suppliercode = base64_decode($fc_suppliercode);

        $supplier = Supplier::find($suppliercode);
        if(!$supplier)
            return response()->json(['status' => 400, 'message' => 'Not Found! Supplier tidak ditemukan pada system'], 400);

        $delete = $supplier->delete();
        if($delete)
            return response()->json(['status' => 201, 'message' => 'Berhasil menghapus supplier'], 201);
        return response()->json(['status' => 400, 'message' => 'Delete Fail! Gagal menghapus supplier'], 400);
    }
}
