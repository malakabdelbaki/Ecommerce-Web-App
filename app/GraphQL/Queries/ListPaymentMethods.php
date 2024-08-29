<?php

namespace App\GraphQL\Queries;

use App\Models\PaymentMethod;

class ListPaymentMethods
{
    public function resolve()
    {
        return PaymentMethod::all();
    }
}
