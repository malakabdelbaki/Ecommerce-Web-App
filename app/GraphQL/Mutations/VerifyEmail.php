<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Nuwave\Lighthouse\Exceptions\AuthorizationException;

class VerifyEmail
{
    /**
     * Create a new class instance.
     */
   public function resolve($rootValue, array $args){
       // Retrieve the user by the provided token and check if the token is not expired
       $user = User::where('hashed_email_verification_token', $args['token'])
           ->where('email_verification_token_expires_at', '>', Carbon::now()->toDateTimeString())
           ->first();

       if (!$user || !Hash::check($user->email_verification_token, $args['token'])) {
//           throw new \Exception('Invalid or expired token.');
           return [
               'success' => false,
               'message' => 'Invalid Token, expected: '.$user->hashed_email_verification_token.'
               for user: '.$user->email
           ];
       }

       // Mark the user as verified
       $user->email_verified_at = Carbon::now();
       $user->email_verification_token = null; // Remove the token
       $user->email_verification_token_expires_at = null; // Remove the expiration timestamp
       $user->save();

       return [
           'success' => true,
           'message' => 'Email verified successfully.'
       ];
   }
}