<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller {
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Store a newly created resource in storage.
     */
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

    public function profile() {
        if(!Auth::user()) {
            return response()->json([
                'message' => 'Unauthenticated',
                'success' => false,
                'data' => null
            ], 401);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'success' => false,
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'User profile retrieved successfully',
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function updateProfile(Request $request) {
        $user = Auth::user();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'middle_name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
            'bio' => 'sometimes|string|max:1000',
            'website' => 'sometimes|url|max:255',
            'socials' => 'sometimes|array',
            'location' => 'sometimes|string|max:255',
            'timezone' => 'sometimes|string|max:255',
            'language' => 'sometimes|string|max:255',
            'currency' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'backup_email' => 'sometimes|email|unique:users,backup_email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
                'data' => $validator->errors()
            ], 422);
        }

        // Update the user's profile
        $user->update($request->only([
            'email',
            'password',
            'first_name',
            'last_name',
            'middle_name',
            'slug',
            'username',
            'bio',
            'website',
            'socials',
            'location',
            'timezone',
            'language',
            'currency',
            'phone',
            'backup_email',
        ]));

        // Return success response
        return response()->json([
            'message' => 'Profile updated successfully',
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function updateCV(Request $request) {
        $user = Auth::user();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'cv' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
                'data' => $validator->errors()
            ], 422);
        }

        // save to disk
        $cv = $request->file('cv');
        $cvName = time() . random_bytes(10) . $cv->extension();
        $file = Storage::disk('local')->putFileAs('cv', $cv, $cvName);

        // Update the user's profile
        $user->update($request->only([
            'cv' => $file
        ]));

        // Return success response
        return response()->json([
            'message' => 'CV updated successfully',
            'success' => true,
            'data' => $user
        ], 200);
    }


    public function updateImage(Request $request) {
        $user = Auth::user();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'avatar' => 'sometimes|file|mimes:jpg,jpeg,png|max:10240',
            'background' => 'sometimes|file|mimes:jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
                'data' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('avatar')) {
            // save to disk
            $avatar = $request->file('avatar');
            $avatarName = time() . random_bytes(10) . $avatar->extension();
            $file = Storage::disk('local')->putFileAs('avatar', $avatar, $avatarName);

            // Update the user's profile
            $user->update($request->only([
                'avatar' => $file
            ]));
        }

        if ($request->hasFile('background')) {
            // save to disk
            $background = $request->file('background');
            $backgroundName = time() . random_bytes(10) . $background->extension();
            $file = Storage::disk('local')->putFileAs('background', $background, $backgroundName);

            // Update the user's profile
            $user->update($request->only([
                'background' => $file
            ]));
        }

        // Return success response
        return response()->json([
            'message' => 'Avatar updated successfully',
            'success' => true,
            'data' => $user
        ], 200);
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
}
