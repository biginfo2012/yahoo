<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'shop_id',
        'pagekey',
        'name',
        'display',
        'updatetime',
        'status',
        'get_status',
        'total',
        'start'
    ];
}
