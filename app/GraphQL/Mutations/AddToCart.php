<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AddToCart
{
    /**
     * Create a new class instance.
     */
    public function resolve($root, array $args)
    {
        $validator = Validator::make($args,[
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $user = Auth::user();

        if(!$user){
            throw new \Exception("User not found");
        }

        $product = Product::find($args['product_id']);


        if($args['quantity']>$product->stock){
            throw new \Exception("Only $product->stock items are available");
        }

        $cart = Cart::firstOrCreate([
            'user_id' => $user->id
        ]);

        $existingCartItem  = $cart->products()->where('product_id', $args['product_id'])->first();

        if($existingCartItem){
            $newQuantity = $existingCartItem->pivot->quantity + $args['quantity'];
            $cart->products()->updateExistingPivot($product->id, ['quantity' => $newQuantity]);
            $check = 'old';
        }else {
            $cart->products()->attach($product->id, ['quantity' => $args['quantity']]);
            $check = 'new';
        }

        return [
            'user' => $user,
            'message' => 'Product added to cart successfully '.$check,
            'errors'=>[],
        ];
    }
}
