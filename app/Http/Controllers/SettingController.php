<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $company = CompanySetting::firstOrCreate(
            ['id' => 1],
            ['company_name' => 'Inkora Digital Printing']
        );

        /** @var User $currentUser */
        $currentUser = Auth::user();
        $users = $currentUser->isOwner() ? User::orderBy('id')->get() : collect();

        return view('settings.index', compact('company', 'users'));
    }

    public function updateCompany(Request $request): RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;
        $this->requireOwner();

        $validated = $request->validate([
            'company_name'        => ['required', 'string', 'max:255'],
            'address'             => ['nullable', 'string'],
            'phone'               => ['nullable', 'string', 'max:30'],
            'email'               => ['nullable', 'email', 'max:255'],
            'invoice_footer_note' => ['nullable', 'string'],
            'logo'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'company_name.required' => 'Nama usaha wajib diisi.',
            'logo.image'            => 'File logo harus berupa gambar.',
            'logo.max'              => 'Ukuran logo maksimal 2MB.',
        ]);

        $company = CompanySetting::firstOrCreate(['id' => 1]);

        if ($request->hasFile('logo')) {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($validated);
        $this->logActivity('settings', 'Memperbarui profil usaha perusahaan.');

        return back()->with('success', 'Profil usaha berhasil diperbarui.');
    }

    public function storeUser(Request $request): RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;
        $this->requireOwner();

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', Rule::in(['owner', 'staff'])],
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => $validated['role'],
            'is_active' => true,
        ]);

        $this->logActivity('settings', "Menambahkan akun pengguna baru: {$user->name} ({$user->role}).");

        return back()->with('success', 'Akun pengguna baru berhasil ditambahkan.');
    }

    public function toggleUser(User $user): RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;
        $this->requireOwner();

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $this->logActivity('settings', "Mengubah status aktif akun {$user->email}.");

        return back()->with('success', 'Status akun pengguna berhasil diperbarui.');
    }
}