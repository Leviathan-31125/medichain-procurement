<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempPODTL extends Model
{
    use HasFactory;

    protected $table = 't_temp_podtl';
    protected $primaryKey = 'fn_rownum';
    public $guarded = [
        'fn_rownum',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $incrementing = false;

    public function temppomst () {
        return $this->hasOne(TempPOMST::class, 'fc_pono', 'fc_pono');
    }

    public function stock () {
        return $this->hasOne(Stock::class, 'fc_barcode', 'fc_barcode');
    }
}
