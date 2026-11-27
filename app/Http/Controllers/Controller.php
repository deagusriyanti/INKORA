<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected function requireAuth(): ?RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->is_active) {
            if (Auth::check()) {
                Auth::logout();
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses sistem Inkora.');
        }

        return null;
    }

    protected function requireOwner(): void
    {
        if (!Auth::check() || !Auth::user()->isOwner()) {
            abort(403, 'Akses ditolak. Halaman ini hanya dapat diakses oleh Pemilik Usaha (Owner).');
        }
    }

    protected function logActivity(string $module, string $activity): void
    {
        ActivityLog::create([
            'user_id'    => Auth::id(),
            'module'     => $module,
            'activity'   => $activity,
            'ip_address' => request()->ip(),
        ]);
    }
}