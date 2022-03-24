<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_name',
        'store_account',
        'app_id',
        'prefix'
    ];
    public function app(){
        return $this->hasOne(YahooApp::class, 'id', 'app_id');
    }
}
