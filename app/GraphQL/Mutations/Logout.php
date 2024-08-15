<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;

class Logout
{
    /**
     * Create a new class instance.
     */
    public function __invoke($_, array $args)
    {
        // Get the authenticated user
        $user = Auth::user();

        if (!$user) {
            throw new \Exception("No user is authenticated.");
        }

        // Revoke or invalidate the user's token or session (depends on your setup)
        $guard = Auth::guard('web');
        $guard->logout();

        return [
            'status' => 'SUCCESS',
            'user' => $user
        ];
    }
}
