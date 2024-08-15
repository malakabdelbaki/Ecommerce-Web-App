<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Login
{
    public function __invoke($_, array $args, GraphQLContext $context){

        $validator =  Validator::make($args, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Some errors occurred',
                'errors' => $validator->errors()
            ]);
        }

        $user = User::where('email', $args['email'])->first();
        if(!$user){
            throw new Error('User not found');
        }

        if(!$user->hasVerifiedEmail()){
            throw new \Error('User not verified');
        }

        $guard = Auth::guard();

        if(!$guard->attempt($args)){
            throw new \Error('Invalid credentials');
        }


        return $user;
    }


}
