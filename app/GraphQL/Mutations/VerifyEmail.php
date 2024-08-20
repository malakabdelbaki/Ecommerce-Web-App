<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
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

       $user = User::where('email_verification_token', hash('sha256',$args['token']))
           ->where('email_verification_token_expires_at', '>', Carbon::now()->toDateTimeString())
           ->first();

       if (!$user) {
           return [
               'success' => false,
               'message' => 'Invalid Token. A token can only be used once.'
           ];
       }


       $user->email_verified_at = Carbon::now();
       $user->email_verification_token = null;
       $user->email_verification_token_expires_at = null;
       $user->save();

       return [
           'success' => true,
           'message' => 'Email verified successfully.'
       ];
   }
}
