<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCopy extends Model
{
    use HasFactory;
    protected $fillable = [
        'copy_id',
        'shop_id',
        'status',
        'start'
    ];
}
