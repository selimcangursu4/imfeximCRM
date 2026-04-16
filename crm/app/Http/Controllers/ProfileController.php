<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:Belirtilmemiş,Erkek,Kadın,Diğer',
            'profile_photo' => 'nullable|image|max:2048', // max 2MB
        ]);

        $data = $request->only(['name', 'email', 'phone', 'birth_date', 'gender']);

        // Only allow role/department changes if admin optionally, but for profile, user shouldn't change their own role perhaps? 
        // We will keep role/department out of user's own profile self-update unless they are Admin. Usually handled in User Management.

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo'] = $path;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profil bilgileriniz başarıyla güncellendi.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Şifreniz başarıyla değiştirildi.');
    }
}
