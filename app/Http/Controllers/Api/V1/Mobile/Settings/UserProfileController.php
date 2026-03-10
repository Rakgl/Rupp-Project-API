<?php

namespace App\Http\Controllers\Api\V1\Mobile\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function getUserProfile()
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'delivery_address' => $user->delivery_address,
                'image' => $user->image ? url('storage/' . $user->image) : null, 
            ]
        ]);
    }

    public function updateUserProfile(Request $request) 
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'sometimes|required|string|max:50',
            'email' => 'nullable|email|max:100',
            'delivery_address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $dataToUpdate = $request->only(['name', 'email', 'delivery_address']);

        if ($request->hasFile('image')) {
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $path = $request->file('image')->store('profiles', 'public');
            $dataToUpdate['image'] = $path;
        }
        $user->update($dataToUpdate);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'delivery_address' => $user->delivery_address,
                'image' => $user->image ? url('storage/' . $user->image) : null,
            ]
        ]);
    }
}