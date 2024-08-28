<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use MLL\GraphQLScalars\JSON;

class OrderHistory
{
    /**
     * Create a new class instance.
     */
   public function resolve($root, $args){

       $user = Auth::user();


       if(!$user->hasVerifiedEmail()){
           throw new \Exception("Email not verified");
       }

       $input = $args['input'];
       $query = Order::where('user_id', $user->id);

       if(isset($input['status'])){
           $query->where("status", $input['status']);
       }

       if(isset($input['sort'])){
            $sortField = $input['sort']['field'];
            $sortDirection = $input['sort']['direction'];

            $validSortFields = ['created_at', 'total'];
            if (in_array($sortField, $validSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            }
       }

       $orders = $query
           ->with(['orderItems.product',  'paymentMethod', 'address'])
           ->paginate($input['count']??5, ['*'], 'page',$input['page'] ?? 1);



       $formattedOrders = $orders->getCollection()->map(function ($order) {
           return [
               'id' => $order->id,
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
                   'last_four' => $order->paymentMethod->digits,
               ],
               'created_at' => $order->created_at->toDateTimeString(),
               'updated_at' => $order->updated_at->toDateTimeString(),
           ];
       });


       return [
           'data' => $formattedOrders,
           'pagination' => [
               'currentPage' => $orders->currentPage(),
               'lastPage' => $orders->lastPage(),
               'total' => $orders->total(),
           ],
       ];


   }
}
