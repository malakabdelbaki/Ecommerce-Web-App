<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    //Request contains email and password
    //returns json with keys: status, message, errors
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Some errors occurred',
                'errors' => $validator->errors()
            ]);
        }

        $user = User::where('email', $request->email)->first();
        if ($user && $user->role === 'admin') {

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return response()->json([
                    'status' => true,
                    'message' => 'Login Successful',
                    'errors' => []
                ]);
            } else {
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
