<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SignupController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
        ]);

        $userExists = User::where('email', $request->email)->exists();

        if ($userExists) {
            return response([
                'message' => 'email already registered'
            ], 409);
        }

        $user = User::create([
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        $user->tokens()->delete();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'accessToken' => $token,
            'userData' => $user,
        ]);
    }
}
