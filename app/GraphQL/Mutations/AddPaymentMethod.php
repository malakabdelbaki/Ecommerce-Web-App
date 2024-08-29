<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;

class AddPaymentMethod
{
    public function resolve($root, $args)
    {
        $user = Auth::user();
        $paymentMethodData = $args['input'];

        $user->paymentMethods()->attach($paymentMethodData['paymentMethod_id'], [
            'digits' => $paymentMethodData['digits']
        ]);

        return [
            'success' => true,
            'message' => 'Payment Method has been added'
        ];    }
}
