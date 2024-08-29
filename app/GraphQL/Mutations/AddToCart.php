<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AddToCart
{
    public function resolve($root, array $args)
    {
        $user = Auth::user();
        $input = $args['input'];
        $productId = $input['product_id'];
        $quantityToAdd = $input['quantity'];
        $product = Product::select('id', 'stock')->findOrFail($productId);

        if ($quantityToAdd > $product->stock) {
            throw new \Exception("InsufficientStockException: Only $product->stock items are available");
        }

        $cart = Cart::firstOrCreate([
            'user_id' => $user->id
        ]);
        $existingCartItem  = $cart->products()->where('product_id', $productId)->first();

        if($existingCartItem)
        {
            $newQuantity = $existingCartItem->pivot->quantity + $quantityToAdd;
            $cart->products()->updateExistingPivot($productId, ['quantity' => $newQuantity, 'updated_at' => Carbon::now()]);
        }
        else 
        {
            $cart->products()->attach($productId, ['quantity' => $quantityToAdd, 'created_at' => \Carbon\Carbon::now()]);
        }

        return [
            'user' => $user,
            'message' => 'Product added to cart successfully ',
            'errors'=>[],
        ];
    }
}
