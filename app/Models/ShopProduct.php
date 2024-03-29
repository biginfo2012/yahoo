<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'shop_id',
        'item_code',
        'product_id',
        'status'
    ];
    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
