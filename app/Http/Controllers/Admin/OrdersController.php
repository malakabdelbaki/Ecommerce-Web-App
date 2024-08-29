<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Helpers\OrderFormatter;

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
            $sortField = $request['sort']['field'];
            $sortDirection = $request['sort']['direction'];

            $validSortFields = ['created_at', 'total'];
            if (in_array($sortField, $validSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            }
        }

        $orders = $query
            ->with(['user', 'orderItems.product',  'paymentMethod', 'address'])
            ->paginate($request->input('count', 10), ['*'],  $request['page'] ?? 1);

        $formattedOrders = OrderFormatter::format($orders);

        return response()->json($formattedOrders);

    }
}
