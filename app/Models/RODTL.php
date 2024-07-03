<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RODTL extends Model
{
    use HasFactory;

    protected $table = 't_rodtl';
    protected $primaryKey = 'fn_rownum';
    public $guarded = [
        'fc_rono',
        'fc_rownum',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $incrementing = false;

    public function stock () {
        return $this->hasOne(Stock::class, 'fc_barcode', 'fc_barcode');
    }

    public function romst () {
        return $this->hasOne(ROMST::class, 'fc_rono', 'fc_rono');
    }
}
