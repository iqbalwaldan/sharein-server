<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profilePhoto = $user->getFirstMediaUrl('profile') ?: '/assets/icons/profile-user.png';


        return view('client.user.profile.index', [
            'title' => 'Profile',
            'active' => '',
            'profilePhoto' => $profilePhoto,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'password' => 'required|string',
            // 'file_input' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Check if old password is correct
        if (!Hash::check($request->password, auth()->user()->password)) {
            return response()->json([
                'message' => 'Password is incorrect',
            ], 400);
        }

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ];
        // If new password is provided, update it
        if ($request->filled('new_password')) {
            $data['password'] = Hash::make($request->new_password);
        }

        $user = User::find($id);

        // Save media to database
        if ($request->hasFile('photo_profile')) {
            if ($user->media()->first() != null) {
                $user->media()->first()->delete();
            }
            $user->addMediaFromRequest('photo_profile')->toMediaCollection('profile');
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
        ], 200);
    }
}
