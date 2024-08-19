<?php

namespace App\GraphQL\Mutations;

use App\Models\User;

class resendVerificationEmail
{
    public function resolve($rootValue, array $args)
    {
        // Find the user by email
        $user = User::where('email', $args['email'])->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        if ($user->email_verified_at) {
            return [
                'success' => false,
                'message' => 'Email already verified'
            ];
        }

        $user->sendEmailVerificationNotification();

        return [
            'success' => true,
            'message' => 'Verification email resent successfully'
        ];
    }
}
