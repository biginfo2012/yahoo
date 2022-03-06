<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YahooCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id',
        'page_key',
        'name',
        'display',
        'hidden_page_flag',
        'editing_flag',
        'update_time'
    ];
}
