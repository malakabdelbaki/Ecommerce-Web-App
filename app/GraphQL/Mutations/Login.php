<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Login
{
    public function resolve($root, array $args){

        $input = $args['input'];
        $user = User::where('email', $input['email'])->first();

        if(!$user){
            throw new Error('User not found');
        }

        $guard = Auth::guard();

        if(!$guard->attempt($input)){
            throw new \Error('Invalid credentials');
        }

        return $user;
    }


}
