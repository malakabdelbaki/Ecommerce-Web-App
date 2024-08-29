<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SessionPostRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    public function login(SessionPostRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();
        if ($user)
        {
            if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']]))
            {
                return response()->json([
                    'status' => true,
                    'message' => 'Login Successful',
                    'errors' => []
                ]);
            }
            else
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid password',
                    'errors' => []
                ]);
            }
        }
        else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized or user not found',
                    'errors' => []
                ]);
            }
        }



    public function logout(Request $request){
        Auth::logout();

    }
}
