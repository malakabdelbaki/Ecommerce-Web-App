<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [ 'name', 'description', 'price', 'category_id', 'stock', 'image_url'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function carts(){
        return $this->belongsToMany(Cart::class, 'cart_product')->withPivot('quantity');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product')->withPivot('quantity');
    }
}
