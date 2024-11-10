<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller {
    public function login(Request $request) {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
                'data' => $validator->errors()
            ], 422);
        }

        // Attempt to log the user in
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Authentication passed
            $user = Auth::user();
            return response()->json([
                'message' => 'Login successful',
                'success' => true,
                'data' => [
                    'user' => $user,
                    'auth' => [
                        'token' => $request->user()->createToken('auth_token')->plainTextToken,
                        'token_type' => 'Bearer'
                    ]
                ]
            ], 200);
        } else {
            // Authentication failed
            return response()->json([
                'message' => 'Invalid credentials',
                'success' => false,
                'data' => null
            ], 401);
        }
    }

    public function register(Request $request) {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'role' => 'required|string|in:user,investor,company'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
                'data' => $validator->errors()
            ], 422);
        }

        // Create the user
        $user = User::create([
            'slug' => uniqid(),
            'username' => uniqid(),
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => $request->role
        ]);

        // Return success response
        return response()->json([
            'message' => 'Registration successful',
            'success' => true,
            'data' => [
                'user' => $user,
                'auth' => [
                    'token' => $user->createToken('auth_token')->plainTextToken,
                    'token_type' => 'Bearer'
                ]
            ]
        ], 201);
    }

    public function updatePassword(Request $request) {
        $user = Auth::user();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
                'data' => $validator->errors()
            ], 422);
        }

        // Check if the current password is correct
        if (!password_verify($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Invalid current password',
                'success' => false,
                'data' => null
            ], 401);
        }

        // Update the user's password
        $user->update([
            'password' => bcrypt($request->password)
        ]);

        // Return success response
        return response()->json([
            'message' => 'Password updated successfully',
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function logout() {
        Auth::user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful',
            'success' => true,
            'data' => null
        ], 200);
    }

    public function logoutAll() {
        Auth::user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful',
            'success' => true,
            'data' => null
        ], 200);
    }
}
