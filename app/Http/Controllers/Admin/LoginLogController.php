<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Http\Request;

class LoginLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $query = LoginLog::with('user')->orderBy('login_at', 'desc');

        // Filter by role
        if ($request->has('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }

        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('login_at', $request->date);
        }

        // Search by name or badge
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('user_badge', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => LoginLog::count(),
            'today' => LoginLog::whereDate('login_at', today())->count(),
            'online' => LoginLog::whereNull('logout_at')->count(),
            'admin_logins' => LoginLog::where('role', 'admin')->count(),
            'user_logins' => LoginLog::where('role', 'user')->count(),
        ];

        return view('admin.logs.login-history', compact('logs', 'stats'));
    }
}
