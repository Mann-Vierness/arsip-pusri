<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->ROLE === 'admin' ? 'admin.dashboard' : 'user.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'badge' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('BADGE', $request->badge)->first();

        if (!$user) {
            return back()->withInput()->with('error', 'Badge tidak ditemukan');
        }

        if (!Hash::check($request->password, $user->Password)) {
            return back()->withInput()->with('error', 'Password salah');
        }

        Auth::login($user);

        // LOG LOGIN - FITUR BARU!
        LoginLog::logLogin($user, $request);

        $request->session()->regenerate();

        return redirect()->route($user->ROLE === 'admin' ? 'admin.dashboard' : 'user.dashboard')
            ->with('success', 'Selamat datang, ' . $user->Nama . '!');
    }

    public function logout(Request $request)
    {
        // LOG LOGOUT - FITUR BARU!
        if (Auth::check()) {
            LoginLog::logLogout(Auth::user()->BADGE);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout');
    }
}
