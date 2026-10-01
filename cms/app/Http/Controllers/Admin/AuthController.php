<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function show()
    {
        return Auth::check() ? redirect(Auth::user()->homeUrl()) : view('admin.login');
    }

    public function login(Request $request)
    {
        $cred = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        if (! Auth::attempt($cred + ['active' => true], $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'That email and password do not match an active user.']);
        }
        $request->session()->regenerate();
        $user = Auth::user();

        // Sales roles never land on the website dashboard, even if that was the link they followed.
        $intended = (string) $request->session()->get('url.intended', '');
        if (! Roles::allows($user, 'website') && ! str_contains($intended, '/sales')) {
            $request->session()->forget('url.intended');

            return redirect($user->homeUrl());
        }

        return redirect()->intended($user->homeUrl());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
