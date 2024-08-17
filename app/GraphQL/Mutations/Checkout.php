<?php

namespace App\GraphQL\Mutations;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class Checkout
{
    /**
     * Create a new class instance.
     */
    public function resolve($root, $args)
    {
        $user = Auth::user();

        if(!$user){
            throw new \Exception("User not found");
        }

        if(!$user->hasVerifiedEmail()){
            throw new \Exception("Please verify your email first");
        }

        $input = $args['input'];

        $validator = Validator::make($input, [
            'address_id' => 'required|exists:addresses,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'cart_items' => 'required|array',
            'cart_items.*.product_id' => 'required|exists:products,id',
            'cart_items.*.quantity' => 'required|integer|min:1',
        ]);

        if($validator->fails()){
            throw new \Exception($validator->errors()->first());
        }

        $items = $input['cart_items'];
        DB::beginTransaction();

        try{
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $input['address_id'],
                'payment_method_id' => $input['payment_method_id'],
                'total' => 0,
                'status' => 'Pending',
            ]);


            $total = 0;
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                if($product->stock < $item['quantity']){
                    return [
                        'order_id' => $order->id,
                        'message' => 'Insufficient stock for product: ' . $product->name,
                    ];
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'totalPrice' => $product->price * $item['quantity'],
                ]);

                $total += $product->price * $item['quantity'];
                $product->decrement('stock', $item['quantity']);


            }

            // Update order total
            $order->update(['total' => $total]);

            // Commit transaction
            DB::commit();
            Mail::to(Auth::user()->email)->send(new OrderConfirmation($order));

            return [
                'order_id' => $order->id,
                'message' => 'Order successfully placed!',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Checkout failed: ' . $e->getMessage());
        }

    }
}
