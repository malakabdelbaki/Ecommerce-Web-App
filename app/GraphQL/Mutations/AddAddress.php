<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AddAddress
{
    /**
     * Create a new class instance.
     */
    public function resolve($root, $args)
    {
        $user = Auth::user();
        $address = $args['input'];

        return $user->addresses()->create([
            'label' => $address['label'],
            'name' => $address['name'],
            'address_line1' => $address['address_line1'],
            'address_line2' => $address['address_line2'] ?? null,
            'city' => $address['city'],
            'state' => $address['state'],
            'postal_code' => $address['postal_code'],
            'country' => $address['country'],
            'phone_number' => $address['phone_number'],
        ]);
    }
}
