<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request){

        $query = Order::query();

        if($request->has('status')){
            $query->where('status', $request->input('status'));
        }

        if($request->has('order_id')){
            $query->where('id', $request->input('order_id'));
        }


        if($request->has('sort')){
            switch($request['sort']){
                case 'created_at_asc':
                    $query->orderBy("created_at", "asc");
                    break;
                case 'created_at_desc':
                    $query->orderBy("created_at", "desc");
                    break;
            }
        }

        $orders = $query
            ->with(['user', 'orderItems.product',  'paymentMethod', 'address'])
            ->paginate($request->input('count', 10), ['*'],  $request['page'] ?? 1);

        $formattedOrders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'status' => $order->status,
                'total' => $order->total,
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'name' => $item->product->name, // Access the product relationship directly
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                        'totalPrice' => $item->quantity * $item->product->price,
                    ];
                }),
                'shipping_address' => [
                    'name' => $order->address->name, // Access the address relationship directly
                    'address_line1' => $order->address->address_line1,
                    'address_line2' => $order->address->address_line2,
                    'city' => $order->address->city,
                    'state' => $order->address->state,
                    'postal_code' => $order->address->postal_code,
                    'country' => $order->address->country,
                    'phone_number' => $order->address->phone_number,
                ],
                'payment_method' => [
                    'type' => $order->paymentMethod->type, // Access the payment method relationship directly
                ],
                'created_at' => $order->created_at->toDateTimeString(),
                'updated_at' => $order->updated_at->toDateTimeString(),
            ];
        });

        return response()->json($formattedOrders);

    }
}
