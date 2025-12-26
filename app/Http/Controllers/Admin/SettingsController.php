<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function inputLimitForm()
    {
        $maxPending = config('surat.max_user_pending_documents', 10);
        return view('admin.settings.input_limit', compact('maxPending'));
    }

    public function updateInputLimit(Request $request)
    {
        $request->validate([
            'max_pending' => 'required|integer|min:1|max:100',
        ]);

        // Update value in config/surat.php
        $configPath = config_path('surat.php');
        $configContent = file_get_contents($configPath);
        $configContent = preg_replace_callback(
            "/('max_user_pending_documents'\s*=>\s*)\d+/",
            function($matches) use ($request) {
                return $matches[1] . $request->max_pending;
            },
            $configContent
        );
        file_put_contents($configPath, $configContent);

        // Optional: clear config cache
        if (function_exists('artisan')) {
            \Artisan::call('config:clear');
        }

        return redirect()->route('admin.settings.input-limit')->with('success', 'Batas maksimal input berhasil diubah!');
    }
}
