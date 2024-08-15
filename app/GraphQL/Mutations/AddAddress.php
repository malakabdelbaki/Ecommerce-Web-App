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

        if(!$user){
            throw new \Exception("User not found");
        }

        $validator = Validator::make($args,[
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        // Create the address
        $address = $user->addresses()->create([
            'label' => $args['label'],
            'name' => $args['name'],
            'address_line1' => $args['address_line1'],
            'address_line2' => $args['address_line2'] ?? null,
            'city' => $args['city'],
            'state' => $args['state'],
            'postal_code' => $args['postal_code'],
            'country' => $args['country'],
            'phone_number' => $args['phone_number'],
        ]);

        return $address;
    }
}
