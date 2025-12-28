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

    /**
     * Display a listing of all users (admin & regular user)
     */
    public function index()
    {
        // TAMPILKAN SEMUA USER (ADMIN & USER)
        $users = User::orderBy('ROLE', 'desc') // Admin dulu, baru User
            ->orderBy('BADGE')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in database
     */
    public function store(Request $request)
    {
        $request->validate([
            'badge' => 'required|string|max:50|unique:user,BADGE',
            'Nama' => 'required|string|max:100',
            'password' => 'required|string|min:6',
            'departemen' => 'required|string|max:100',
            'role' => 'required|string|in:user,admin',
        ], [
            'badge.required' => 'Nomor badge wajib diisi',
            'badge.unique' => 'Nomor badge sudah digunakan',
            'Nama.required' => 'Nama wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'departemen.required' => 'Departemen wajib diisi',
            'role.required' => 'Role wajib dipilih',
            'role.in' => 'Role harus User atau Admin',
        ]);

        try {
            $user = User::create([
                'BADGE' => $request->badge,
                'Nama' => $request->Nama,
                'Password' => \Hash::make($request->password),
                'ROLE' => $request->role,
                'Departemen' => $request->departemen,
            ]);

            UserLog::log(
                Auth::user()->BADGE,
                'CREATE_USER',
                "Admin membuat user baru: {$request->Nama} (Badge: {$request->badge}, Role: {$request->role})"
            );

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan dengan role ' . strtoupper($request->role));

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user details
     */
    public function show($badge)
    {
        $user = User::where('BADGE', $badge)->firstOrFail();

        // Statistik dokumen user
        $skCount = $user->suratKeputusan()->count();
        $spCount = $user->suratPerjanjian()->count();
        $addendumCount = $user->suratAddendum()->count();

        // Dokumen terbaru user
        $recentDocuments = collect()
            ->concat($user->suratKeputusan()->orderBy('created_at', 'desc')->limit(5)->get())
            ->concat($user->suratPerjanjian()->orderBy('created_at', 'desc')->limit(5)->get())
            ->concat($user->suratAddendum()->orderBy('created_at', 'desc')->limit(5)->get())
            ->sortByDesc('created_at')
            ->take(10);

        return view('admin.users.show', compact('user', 'skCount', 'spCount', 'addendumCount', 'recentDocuments'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($badge)
    {
        $user = User::where('BADGE', $badge)->firstOrFail();

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in database
     */
    public function update(Request $request, $badge)
    {
        $user = User::where('BADGE', $badge)->firstOrFail();

        $request->validate([
            'Nama' => 'required|string|max:100',
            'password' => 'nullable|string|min:6',
            'departemen' => 'required|string|max:100',
            'role' => 'required|string|in:user,admin',
        ], [
            'Nama.required' => 'Nama wajib diisi',
            'password.min' => 'Password minimal 6 karakter jika diisi',
            'departemen.required' => 'Departemen wajib diisi',
            'role.required' => 'Role wajib dipilih',
            'role.in' => 'Role harus User atau Admin',
        ]);

        try {
            // PROTEKSI: Cegah mengubah role admin terakhir menjadi user
            if ($user->ROLE == 'admin' && $request->role == 'user') {
                $adminCount = User::where('ROLE', 'admin')->count();
                if ($adminCount <= 1) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Tidak dapat mengubah role. Sistem harus memiliki minimal 1 admin.');
                }
            }

            $data = [
                'Nama' => $request->Nama,
                'Departemen' => $request->departemen,
                'ROLE' => $request->role,
            ];

            // Update password hanya jika diisi
            if ($request->filled('password')) {
                $data['Password'] = \Hash::make($request->password);
            }

            $user->update($data);

            UserLog::log(
                Auth::user()->BADGE,
                'UPDATE_USER',
                "Admin mengupdate user: {$user->Nama} (Badge: {$badge}, Role: {$request->role})"
            );

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diupdate');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user from database (soft delete)
     */
    public function destroy($badge)
    {
        $user = User::where('BADGE', $badge)->firstOrFail();

        // PROTEKSI: Cegah menghapus admin terakhir
        if ($user->ROLE === 'admin') {
            $adminCount = User::where('ROLE', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Tidak dapat menghapus admin terakhir. Sistem harus memiliki minimal 1 admin.');
            }
        }

        try {
            $userName = $user->Nama;
            $userRole = $user->ROLE;
            
            // Soft delete
            $user->delete();

            UserLog::log(
                Auth::user()->BADGE,
                'DELETE_USER',
                "Admin menghapus user: {$userName} (Badge: {$badge}, Role: {$userRole})"
            );

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}