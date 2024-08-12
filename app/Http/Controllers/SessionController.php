<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function login(Request $request)
    {
        $validator =  Validator::make($request->all(), [
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

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            if($user->role = 'admin'){
                return response()->json([
                    'status' => true,
                    'sesion' => Session::token(),
                    'message' => 'Login Successful',
                ]);
            }
            else{
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ]);
            }
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'invalid email or password',
            ]);
        }

    }

    public function logout(Request $request){
        Auth::logout();
    }
}
