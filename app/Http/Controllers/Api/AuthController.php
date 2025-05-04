<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login user and create token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse('api.failed', $validator->errors()->toArray(), 422, 422);
        }

        // Check email and password
        if (!Auth::attempt($request->only('email', 'password'))) {
            return apiResponse('api.failed', [], 401, 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return apiResponse('api.success', [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }


    /**
     * Log the user out (Invalidate the token)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return apiResponse('api.success');
    }

    /**
     * Get the authenticated User
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return apiResponse('user.profile', ['user' => $request->user()]);
    }
}
