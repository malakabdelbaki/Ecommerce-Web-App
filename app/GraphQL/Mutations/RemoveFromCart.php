<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class RemoveFromCart
{
    public function resolve($root, array $args)
    {
        $user = Auth::user();
        $input = $args['input'];

        $productId = $input['product_id'];
        $quantityToRemove = $input['quantity'];
        $product = Product::find($productId);

        if (!$product) {
            throw new \Exception("ProductNotFoundException: Product with ID $productId does not exist");
        }

        // Find or create the cart
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id
        ]);

        $cartItem = $cart->products()->where('product_id', $productId)->first();

        if (!$cartItem) {
            throw new \Exception("ProductNotInCartException: Product with ID $productId is not in the cart");
        }

        $currentQuantity = $cartItem->pivot->quantity;

        if ($quantityToRemove >= $currentQuantity) {
            $cart->products()->detach($productId);
            $message = 'Product removed from cart successfully';
        } else {
            $newQuantity = $currentQuantity - $quantityToRemove;
            $cart->products()->updateExistingPivot($productId, ['quantity' => $newQuantity, 'updated_at' => Carbon::now()]);
            $message = 'Product quantity updated successfully';
        }

        return [
            'user' => $user,
            'message' => $message,
            'errors' => [],
        ];
    }
    }

