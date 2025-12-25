<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $users = User::where('ROLE', 'user')
            ->orderBy('BADGE')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'badge' => 'required|string|max:50|unique:user,BADGE',
            'Nama' => 'required|string|max:100',
            'password' => 'required|string|min:6',
            'departemen' => 'required|string|max:100',
        ], [
            'badge.required' => 'Nomor badge wajib diisi',
            'badge.unique' => 'Nomor badge sudah digunakan',
            'Nama.required' => 'Nama wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'departemen.required' => 'Departemen wajib diisi',
        ]);

        try {
            $user = User::create([
                'BADGE' => $request->badge,
                'Nama' => $request->Nama,
                'Password' => \Hash::make($request->password),
                'ROLE' => 'user',
                'Departemen' => $request->departemen,
            ]);

            UserLog::log(
                Auth::user()->BADGE,
                'CREATE_USER',
                "Admin membuat user baru: {$request->Nama} (Badge: {$request->badge})"
            );

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function show($badge)
    {
        $user = User::where('BADGE', $badge)->firstOrFail();

        $skCount = $user->suratKeputusan()->count();
        $spCount = $user->suratPerjanjian()->count();
        $addendumCount = $user->suratAddendum()->count();

        $recentDocuments = collect()
            ->concat($user->suratKeputusan()->orderBy('created_at', 'desc')->limit(5)->get())
            ->concat($user->suratPerjanjian()->orderBy('created_at', 'desc')->limit(5)->get())
            ->concat($user->suratAddendum()->orderBy('created_at', 'desc')->limit(5)->get())
            ->sortByDesc('created_at')
            ->take(10);

        return view('admin.users.show', compact('user', 'skCount', 'spCount', 'addendumCount', 'recentDocuments'));
    }

    public function edit($badge)
    {
        $user = User::where('BADGE', $badge)->firstOrFail();

        if ($user->ROLE === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat mengedit user admin');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $badge)
    {
        $user = User::where('BADGE', $badge)->firstOrFail();

        if ($user->ROLE === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat mengedit user admin');
        }

        $request->validate([
            'Nama' => 'required|string|max:100',
            'password' => 'nullable|string|min:6',
            'departemen' => 'required|string|max:100',
        ]);

        try {
            $data = [
                'Nama' => $request->Nama,
                'Departemen' => $request->departemen,
            ];

            if ($request->filled('password')) {
                $data['Password'] = \Hash::make($request->password);
            }

            $user->update($data);

            UserLog::log(
                Auth::user()->BADGE,
                'UPDATE_USER',
                "Admin mengupdate user: {$user->Nama} (Badge: {$badge})"
            );

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diupdate');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate user: ' . $e->getMessage());
        }
    }

    public function destroy($badge)
    {
        $user = User::where('BADGE', $badge)->firstOrFail();

        if ($user->ROLE === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat menghapus user admin');
        }

        try {
            $userName = $user->Nama;
            $user->delete(); // Soft delete

            UserLog::log(
                Auth::user()->BADGE,
                'DELETE_USER',
                "Admin menghapus user: {$userName} (Badge: {$badge})"
            );

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}
