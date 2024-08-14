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
    /**
     * Create a new class instance.
     */
    public function resolve($root, array $args)
    {
        $validator = Validator::make($args, [
            'name' => ['required', 'string', ' max:255'],
            'email' => ['required', 'email', 'unique:users', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            return [
                'errors' => collect($validator->errors()->all())->map(function ($error, $key) use ($validator) {
                    return ['field' => array_keys($validator->failed())[$key], 'message' => $error];
                }),
            ];
        }
        $user = User::create([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => Hash::make($args['password']),
            'email_verification_token' => Str::random(60),
        ]);

        $user->email_verification_token = Str::random(60);
        $user->save();

        $user->sendEmailVerificationNotification();
//        event(new Registered($user));

        return [
            'user' => $user,
            'message' => 'Registration successful. Please check your email for verification.',
            'errors' => [],
        ];
    }

}

