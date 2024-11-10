<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller {
    public function updateProfile(Request $request) {
        $user = Auth::user();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'middle_name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|min:4|max:255|unique:users,username,' . $user->id,
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

    public function updateAvatar(Request $request) {
        $user = Auth::user();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|file|mimes:jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
                'data' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('avatar')) {
            // Save to disk
            $avatar = $request->file('avatar');
            $avatarName = time() . bin2hex(random_bytes(5)) . '.' . $avatar->extension();
            $file = Storage::disk('public')->putFileAs('avatar', $avatar, $avatarName);

            // Update the user's profile
            $user->update([
                'avatar' => Storage::url($file)
            ]);
        }

        // Return success response
        return response()->json([
            'message' => 'Avatar updated successfully',
            'success' => true,
            'data' => $user
        ], 200);
    }

    /**
     * Update the user's background image.
     */
    public function updateCover(Request $request) {
        $user = Auth::user();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'cover' => 'required|file|mimes:jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
                'data' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('cover')) {
            // Save to disk
            $background = $request->file('cover');
            $backgroundName = time() . bin2hex(random_bytes(5)) . '.' . $background->extension();
            $file = Storage::disk('public')->putFileAs('background', $background, $backgroundName);

            // Update the user's profile
            $user->update([
                'background' => Storage::url($file)
            ]);
        }

        // Return success response
        return response()->json([
            'message' => 'Background updated successfully',
            'success' => true,
            'data' => $user
        ], 200);
    }
}
