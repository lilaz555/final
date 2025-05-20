<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function loginPost(Request $request)
    {

        $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required'
        ]);
        if (!Auth::attempt($request->only(['email', 'password']))) {
            $message = 'email & password does not match with our record.';
            return response()->json([
                'data' => [],
                'status' => 0,
                'message' => $message
            ], 500);
        }
        $user = User::query()->where('email', '=', $request['email'])->first();

        $token = $user->createToken('auth_token')->plainTextToken;
        $data = [];
        $data['user'] = $user;
        $data['token'] = $token;//for postman

        return response()->json([
            'status' => 1,
            'data' => $data,
            'message' => 'User logged Successful',
        ]);
    }

    public function registrationPost(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::query()->create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' =>$request['password'],   // تشفير كلمة المرور
        ]);

        $token = $user->createToken("API TOKEN")->plainTextToken;

        return response()->json([
            'status' => 1,
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
            'message' => 'User created successfully'
        ]);

    }
    public function logout(Request $request): JsonResponse
    {
        // جلب المستخدم من التوكن
        $user = $request->user();

        if ($user) {
            // حذف التوكن الحالي
            $user->currentAccessToken()->delete();

            return response()->json([
                'status' => 1,
                'message' => 'User logged out successfully'
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'User not authenticated'
        ], 401);
    }

    public function viewuser(): JsonResponse
    {
        $user = User::all();

        return response()->json([
            'status' => 1,
            'data' => $user,
            'message' => 'User retrieved successfully'
        ]);
    }

}
