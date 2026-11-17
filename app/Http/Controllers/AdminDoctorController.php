<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Poli;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminDoctorController extends Controller
{
    /**
     * Display a listing of doctors with user accounts and specialties.
     */
    public function index(Request $request): View|JsonResponse
    {
        $admin = Auth::user();
        $user = [
            'name' => $admin?->name ?? 'Administrator',
            'email' => $admin?->email ?? 'admin@aihealth.com',
            'role' => 'Administrator',
            'initials' => $admin ? strtoupper(substr($admin->name, 0, 1).substr(trim(strstr($admin->name, ' ') ?: 'A'), 1, 1)) : 'AD',
            'is_logged_in' => true,
            'is_admin' => true,
        ];

        $search = trim($request->get('q', ''));
        $poliId = $request->get('poli_id');

        $query = Doctor::with(['poli', 'user', 'schedules'])
            ->withCount('bookings');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_dokter', 'like', "%{$search}%")
                    ->orWhere('spesialisasi', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('poli', function ($pq) use ($search) {
                        $pq->where('nama_poli', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($poliId)) {
            $query->where('poli_id', $poliId);
        }

        $doctors = $query->latest('id')->get();
        $polis = Poli::orderBy('nama_poli')->get();

        // Calculate doctor management statistics
        $totalDoctors = Doctor::count();
        $totalPolis = Poli::count();
        $doctorsWithAccount = Doctor::whereNotNull('user_id')->count();
        $avgConsultationFee = (float) Doctor::avg('price_num');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'doctors' => $doctors,
                'stats' => [
                    'total_doctors' => $totalDoctors,
                    'total_polis' => $totalPolis,
                    'doctors_with_account' => $doctorsWithAccount,
                    'avg_fee' => $avgConsultationFee,
                ],
            ]);
        }

        return view('admin.doctors.index', [
            'admin' => $admin,
            'user' => $user,
            'role' => 'admin',
            'doctors' => $doctors,
            'polis' => $polis,
            'search' => $search,
            'selectedPoliId' => $poliId,
            'stats' => [
                'total_doctors' => $totalDoctors,
                'total_polis' => $totalPolis,
                'doctors_with_account' => $doctorsWithAccount,
                'avg_fee' => $avgConsultationFee,
            ],
        ]);
    }

    /**
     * Store a newly created doctor and their login user account.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nama_dokter' => ['required', 'string', 'max:255'],
            'poli_id' => ['required', 'exists:polis,id'],
            'spesialisasi' => ['nullable', 'string', 'max:255'],
            'price_num' => ['required', 'numeric', 'min:0'],
            'availability' => ['nullable', 'string', 'max:100'],
            'experience' => ['nullable', 'string', 'max:100'],
            'patients' => ['nullable', 'string', 'max:50'],
            'rating' => ['nullable', 'numeric', 'between:1,5'],
            'image_preset' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            // Doctor User Account fields
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $poli = Poli::findOrFail($validated['poli_id']);
        $spesialisasi = ($validated['spesialisasi'] ?? null) ?: ($poli->name_en ?? $poli->nama_poli);
        $priceNum = (float) $validated['price_num'];
        $priceFormatted = 'Rp '.number_format($priceNum, 0, ',', '.');
        $initials = $this->generateInitials($validated['nama_dokter']);

        // Handle doctor profile picture
        $imagePath = $validated['image_preset'] ?? 'images/doctors/doctor-1.png';
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $fileName = 'doctor_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images/doctors'), $fileName);
            $imagePath = 'images/doctors/'.$fileName;
        }

        $doctor = DB::transaction(function () use ($validated, $spesialisasi, $priceNum, $priceFormatted, $initials, $imagePath) {
            // 1. Create the user account with role 'doctor'
            $user = User::create([
                'name' => $validated['nama_dokter'],
                'email' => strtolower(trim($validated['email'])),
                'password' => Hash::make($validated['password']),
                'role' => 'doctor',
            ]);

            // 2. Create the doctor profile linked to user account
            $doc = Doctor::create([
                'user_id' => $user->id,
                'poli_id' => $validated['poli_id'],
                'nama_dokter' => $validated['nama_dokter'],
                'spesialisasi' => $spesialisasi,
                'rating' => $validated['rating'] ?? 4.8,
                'price' => $priceFormatted,
                'price_num' => $priceNum,
                'initials' => $initials,
                'image' => $imagePath,
                'availability' => ($validated['availability'] ?? null) ?: '09:00 - 17:00 WIB',
                'experience' => ($validated['experience'] ?? null) ?: '5 tahun',
                'patients' => ($validated['patients'] ?? null) ?: '1.0k Up',
            ]);

            // 3. Create default weekly schedule so doctor can be booked immediately
            Schedule::create([
                'doctor_id' => $doc->id,
                'hari' => 'Senin - Jumat',
                'jam_mulai' => '09:00',
                'jam_selesai' => '17:00',
                'kuota' => 20,
            ]);

            return $doc;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokter '.$doctor->nama_dokter.' dan akun portal ('.$doctor->user?->email.') berhasil didaftarkan!',
                'doctor' => $doctor->load(['poli', 'user']),
            ], 201);
        }

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Dokter '.$doctor->nama_dokter.' dan akun login ('.$doctor->user?->email.') berhasil didaftarkan ke sistem Klinik Maristella!');
    }

    /**
     * Get doctor details for editing (JSON).
     */
    public function show(Doctor $doctor): JsonResponse
    {
        return response()->json([
            'success' => true,
            'doctor' => $doctor->load(['poli', 'user', 'schedules']),
        ]);
    }

    /**
     * Update an existing doctor and their login user account.
     */
    public function update(Request $request, Doctor $doctor): RedirectResponse|JsonResponse
    {
        $userId = $doctor->user_id;

        $validated = $request->validate([
            'nama_dokter' => ['required', 'string', 'max:255'],
            'poli_id' => ['required', 'exists:polis,id'],
            'spesialisasi' => ['nullable', 'string', 'max:255'],
            'price_num' => ['required', 'numeric', 'min:0'],
            'availability' => ['nullable', 'string', 'max:100'],
            'experience' => ['nullable', 'string', 'max:100'],
            'patients' => ['nullable', 'string', 'max:50'],
            'rating' => ['nullable', 'numeric', 'between:1,5'],
            'image_preset' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            // Doctor User Account fields
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $poli = Poli::findOrFail($validated['poli_id']);
        $spesialisasi = ($validated['spesialisasi'] ?? null) ?: ($poli->name_en ?? $poli->nama_poli);
        $priceNum = (float) $validated['price_num'];
        $priceFormatted = 'Rp '.number_format($priceNum, 0, ',', '.');
        $initials = $this->generateInitials($validated['nama_dokter']);

        // Handle image update
        $imagePath = $doctor->image;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $fileName = 'doctor_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images/doctors'), $fileName);
            $imagePath = 'images/doctors/'.$fileName;
        } elseif (! empty($validated['image_preset'])) {
            $imagePath = $validated['image_preset'];
        }

        DB::transaction(function () use ($doctor, $validated, $spesialisasi, $priceNum, $priceFormatted, $initials, $imagePath) {
            // 1. Update or create the User account
            if ($doctor->user) {
                $userUpdates = [
                    'name' => $validated['nama_dokter'],
                    'email' => strtolower(trim($validated['email'])),
                    'role' => 'doctor',
                ];

                if (! empty($validated['password'])) {
                    $userUpdates['password'] = Hash::make($validated['password']);
                }

                $doctor->user->update($userUpdates);
            } else {
                $user = User::create([
                    'name' => $validated['nama_dokter'],
                    'email' => strtolower(trim($validated['email'])),
                    'password' => Hash::make($validated['password'] ?? 'password123'),
                    'role' => 'doctor',
                ]);
                $doctor->user_id = $user->id;
            }

            // 2. Update Doctor profile
            $doctor->update([
                'user_id' => $doctor->user_id,
                'poli_id' => $validated['poli_id'],
                'nama_dokter' => $validated['nama_dokter'],
                'spesialisasi' => $spesialisasi,
                'rating' => $validated['rating'] ?? $doctor->rating,
                'price' => $priceFormatted,
                'price_num' => $priceNum,
                'initials' => $initials,
                'image' => $imagePath,
                'availability' => ($validated['availability'] ?? null) ?: $doctor->availability,
                'experience' => ($validated['experience'] ?? null) ?: $doctor->experience,
                'patients' => ($validated['patients'] ?? null) ?: $doctor->patients,
            ]);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data dokter '.$doctor->nama_dokter.' dan akun berhasil diperbarui!',
                'doctor' => $doctor->fresh(['poli', 'user']),
            ]);
        }

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Data dokter '.$doctor->nama_dokter.' dan akun berhasil diperbarui!');
    }

    /**
     * Delete a doctor and their associated login account safely.
     */
    public function destroy(Doctor $doctor): RedirectResponse|JsonResponse
    {
        $doctorName = $doctor->nama_dokter;
        $user = $doctor->user;

        DB::transaction(function () use ($doctor, $user) {
            // Delete schedules
            $doctor->schedules()->delete();

            // Delete doctor profile
            $doctor->delete();

            // Delete associated user account if role is doctor
            if ($user && $user->role === 'doctor') {
                $user->delete();
            }
        });

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokter '.$doctorName.' dan akun login berhasil dihapus dari sistem.',
            ]);
        }

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Dokter '.$doctorName.' dan akun login berhasil dihapus dari sistem.');
    }

    /**
     * Generate initials from doctor name (e.g. Dr. Budi Santoso -> BS).
     */
    private function generateInitials(string $name): string
    {
        $clean = trim(str_replace(['Dr. ', 'Dr.', 'dr. ', 'dr.', ', Sp.S', ', Sp.JP', ', Sp.OT', ', Sp.A', ', Sp.PD'], '', $name));
        $words = preg_split('/\s+/', $clean);

        if (! empty($words[0]) && ! empty($words[1])) {
            return strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
        }

        if (! empty($words[0])) {
            return strtoupper(substr($words[0], 0, min(2, strlen($words[0]))));
        }

        return 'DR';
    }
}
