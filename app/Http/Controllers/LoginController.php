<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8',
        ]);
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken($user->role)->plainTextToken;
            
            switch ($user->role_id) {
                case 1:
                    return response()->json([
                        'token' => $token,
                        'role' => 'Super_Admin'
                    ]);
                case 2:
                    return response()->json([
                        'token' => $token,
                        'role' => 'Academy_AdminStrator'
                    ]);
                case 3:
                    return response()->json([
                        'token' => $token,
                        'role' => 'Teacher'
                    ]);
                case 4:
                    return response()->json([
                        'token' => $token,
                        'role' => 'Student'
                    ]);
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }
    }
}
