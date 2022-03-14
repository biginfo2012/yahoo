<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YahooCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id',
        'category_code',
        'category_name',
        'display',
        'is_leaf',
        'status',
        'update_date'
    ];
}
