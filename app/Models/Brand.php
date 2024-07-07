<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 't_brand';
    protected $primaryKey = 'fc_brandcode';
    public $guarded = [
        'fc_brandcode',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $incrementing = false;
}
