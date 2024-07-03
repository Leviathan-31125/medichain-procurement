<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 't_supplier';
    protected $primaryKey = 'fc_suppliercode';
    public $guarded = [
        'fc_suppliercode',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $incrementing = false;

    public function bank() {
        return $this->hasOne(TRXType::class, 'fc_trxcode', 'fc_supplierbank');
    }
}
