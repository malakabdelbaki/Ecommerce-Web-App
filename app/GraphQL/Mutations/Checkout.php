<?php

namespace App\GraphQL\Mutations;

use App\Mail\OrderConfirmation;
use App\Models\Cart;
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

        if(!$user->hasVerifiedEmail()){
            throw new \Exception("Please verify your email first");
        }

        $input = $args['input'];


        $cart = Cart::with('products')->findOrFail($input['cart_id']);
        $items = $cart->products;
        DB::beginTransaction();

        try{
            foreach ($items as $item) {
                if ($item->stock < $item->pivot->quantity) {
                    throw new \Exception('Insufficient stock for product: ' . $item->name);
                }
            }

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $input['address_id'],
                'payment_method_id' => $input['payment_method_id'],
                'total' => 0,
                'status' => 'Pending',
            ]);


            $total = 0;
            $orderItems = [];
            foreach ($items as $item) {
                $quantity = $item->pivot->quantity;
                $orderItems[] = [
                    'order_id' => $order->id,
                    'product_id' => $item->id,
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $total += $item->price * $quantity;
                $item->decrement('stock', $quantity);
            }

            OrderItem::insert($orderItems);

            $order->update(['total' => $total]);

            DB::commit();

            Mail::to(Auth::user()->email)->queue(new OrderConfirmation($order));

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
