<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ROMST extends Model
{
    use HasFactory;

    protected $table = 't_romst';
    protected $primaryKey = 'fc_rono';
    public $guarded = [
        'fc_rono',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $incrementing = false;

    public function rodtl () {
        return $this->hasMany(RODTL::class, 'fc_rono', 'fc_rono');
    }

    public function pomst () {
        return $this->hasOne(POMST::class, 'fc_pono', 'fc_pono');
    }

    public function warehouse () {
        return $this->hasOne(Warehouse::class, 'fc_warehousecode', 'fc_warehousecode');
    }
}
