<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::attempt($request->only("email", "password"))) {
            return response()->json(
                [
                    'message' => 'Authorized',
                    "status" => 200,
                    'accessToken' =>  $request->user()->createToken('invoice')->plainTextToken,
                ],
                status: 200
            );
        }
        return response()->json(['status' => 'Not authorized'], 403);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['status' => 'Token revoked'], 200);
    }
}
