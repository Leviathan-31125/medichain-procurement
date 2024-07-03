<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TRXType extends Model
{
    use HasFactory;

    protected $table = 't_trxtype';
    protected $primaryKey = 'fc_trxcode';
    public $guarded = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $incrementing = false;
}
