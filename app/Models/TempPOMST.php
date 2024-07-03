<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempPOMST extends Model
{
    use HasFactory;

    protected $table = 't_temp_pomst';
    protected $primaryKey = 'fc_pono';
    public $guarded = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $incrementing = false;

    public function temppodtl () {
        return $this->hasMany(TempPODTL::class, 'fc_pono', 'fc_pono');
    }

    public function supplier () {
        return $this->hasOne(Supplier::class, 'fc_suppliercode', 'fc_suppliercode');
    }
}
