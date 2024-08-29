<?php

namespace App\GraphQL\Mutations;

use App\Mail\VerificationEmail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterUser
{
    public function resolve($root, array $args)
    {
        $input = $args["input"];
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $user->save();
        $user->sendEmailVerificationNotification();

        return [
            'user' => $user,
            'message' => 'Registration successful. Please check your email for verification.',
            'errors' => [],
        ];
    }

}

