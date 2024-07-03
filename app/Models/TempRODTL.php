<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempRODTL extends Model
{
    use HasFactory;

    protected $table = 't_temp_rodtl';
    protected $primaryKey = 'fn_rownum';
    public $guarded = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $incrementing = false;

    public function stock () {
        return $this->hasOne(Stock::class, 'fc_barcode', 'fc_barcode');
    }

    public function tempromst () {
        return $this->hasOne(TempROMST::class, 'fc_rono', 'fc_rono');
    }
}
