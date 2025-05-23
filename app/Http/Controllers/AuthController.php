<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * create a new AuthController instance
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api',['except'=>['login','register']]);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                $validator->errors()], 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {     //هذا الايميل لم يسجل الحساب
            return response()->json([
                'status' => 0,
                'error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'status' => 1,
            'message' => 'User logged in successfully',
            'user' => auth()->user(),
            'access_token' => $token
        ]);
    }

    /**
     * Register a User.
     *
     *
     */

    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 0,
                $validator->errors()->toJson()], 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'status' => 1,
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     *
     */
    public function logout(): \Illuminate\Http\JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'status' => 1,
            'message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     *
     */
    public function refresh(): \Illuminate\Http\JsonResponse
    {
        return $this->createNewToken(auth('api')->refresh());
    }

    /**
     * Get the authenticated User.
     *
     *
     */
    public function userProfile(): \Illuminate\Http\JsonResponse
    {
        return response()->json([auth('api')->user()]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     *
     */
    protected function createNewToken($token): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()
        ]);
    }


}
