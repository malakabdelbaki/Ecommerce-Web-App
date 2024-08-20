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

            $failed = $validator->failed();

            $errors = collect($validator->errors()->all())->map(function ($error) use ($validator, $failed) {
                // Find the first field that has this error message.
                foreach ($failed as $field => $fail) {
                    if (in_array($error, $validator->errors()->get($field))) {
                        return ['field' => $field, 'message' => $error];
                    }
                }
                // Fallback if no match is found (shouldn't happen, but just in case).
                return ['field' => null, 'message' => $error];
           });
            return ['errors' => $errors];

        }
        $user = User::create([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => Hash::make($args['password']),
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

