<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use MLL\GraphQLScalars\JSON;
use App\Helpers\OrderFormatter;


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

       $formattedOrders = OrderFormatter::format($orders);

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
