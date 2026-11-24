<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's profile view.
     */
    public function edit(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Eager load associated doctor or patient profile if present
        if ($user->isDoctor()) {
            $user->loadMissing('doctor.poli');
        } elseif ($user->isUser()) {
            $user->loadMissing('patient');
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-\.]+$/',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'username.regex' => 'Username hanya boleh berisi huruf, angka, garis bawah, strip, atau titik.',
            'avatar.max' => 'Ukuran foto profil tidak boleh melebihi 2MB.',
            'avatar.image' => 'File foto profil harus berupa gambar yang valid (JPEG, PNG, JPG, WEBP).',
        ]);

        // Process avatar upload via Laravel Storage
        if ($request->hasFile('avatar')) {
            // Delete old custom avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Update allowed profile fields only (id and role are strictly excluded)
        $user->name = $validated['name'];
        $user->username = ! empty($validated['username']) ? strtolower(trim($validated['username'])) : null;
        $user->email = strtolower(trim($validated['email']));
        $user->phone = ! empty($validated['phone']) ? trim($validated['phone']) : null;
        $user->save();

        // Sync name/phone with linked Doctor or Patient model
        if ($user->isDoctor() && $user->doctor) {
            $user->doctor->update([
                'nama_dokter' => $user->name,
                'initials' => $user->initials,
            ]);
        }

        if ($user->isUser() && $user->patient) {
            $user->patient->update([
                'full_name' => $user->name,
                'phone' => $user->phone ?? $user->patient->phone,
            ]);
        }

        return redirect()->route('profile.edit')->with('success', 'Profil Anda berhasil diperbarui!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'Kata sandi baru harus memiliki panjang minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        // Verify current password
        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Kata sandi lama yang Anda masukkan tidak sesuai.',
            ]);
        }

        // Store hashed password
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Kata sandi Anda berhasil diperbarui!');
    }

    /**
     * Remove custom profile photo and revert to default avatar.
     */
    public function removeAvatar(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->avatar = null;
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil dihapus dan dikembalikan ke foto default.');
    }

    /**
     * Fallback to serve files stored in storage/app/public when symlink is not available on Windows.
     */
    public function serveStorage(string $path): BinaryFileResponse
    {
        $filePath = storage_path('app/public/'.ltrim($path, '/'));

        if (! file_exists($filePath)) {
            abort(404);
        }

        return response()->file($filePath);
    }
}
