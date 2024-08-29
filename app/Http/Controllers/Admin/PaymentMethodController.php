<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentMethodPostRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function store(PaymentMethodPostRequest $request)
    {
        $PaymentMethod = PaymentMethod::create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Payment Method created successfully',
            'category' => $PaymentMethod
        ], 201);
    }
}
