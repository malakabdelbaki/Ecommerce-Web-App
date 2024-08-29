<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;

class Logout
{
    public function resolve($root, array $args)
    {
        $user = Auth::user();

        if (!$user) {
            throw new \Exception("No user is authenticated.");
        }
        
        $guard = Auth::guard('web');
        $guard->logout();

        return [
            'status' => 'SUCCESS',
            'user' => $user
        ];
    }
}
