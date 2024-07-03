<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 't_supplier';
    protected $primaryKey = 'fc_suppliercode';
    public $guarded = [
        'fc_suppliercode',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $incrementing = false;
}
