<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class InfoUserController extends Controller
{
    public function create()
    {
        return view('laravel-examples.user-profile');
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->id)],
            'phone' => ['max:50'],
            'location' => ['max:70'],
            'about_me' => ['max:150'],
            'image' => ['image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->storeAs('public/image', $image->hashName());
            $attributes['image'] = $image->hashName();
        } else {
            $attributes['image'] = Auth::user()->image;
        }

        if ($request->get('email') != Auth::user()->email) {
            if (env('IS_DEMO') && Auth::user()->id == 1) {
                $msg = 'You are in a demo version, you can\'t change the email address.';
                return $request->ajax()
                    ? response()->json(['message' => $msg], 400)
                    : redirect()->back()->withErrors(['email' => $msg]);
            }
        }

        Auth::user()->update($attributes);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => Auth::user()
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

}
