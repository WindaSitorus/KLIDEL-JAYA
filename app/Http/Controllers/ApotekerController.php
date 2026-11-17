<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Obat;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApotekerController extends Controller
{
    /**
     * Display Apoteker Dashboard: Informasi Obat & Stok Opname.
     */
    public function index(): View
    {
        $authUser = Auth::user();

        $apoteker = [
            'name' => $authUser?->name ?? 'Amélie Laurent',
            'email' => $authUser?->email ?? 'apoteker@aihealth.com',
            'role' => 'Apoteker',
            'initials' => $authUser
                ? strtoupper(
                    substr($authUser->name, 0, 1).
                    substr(trim(strstr($authUser->name, ' ') ?: 'L'), 1, 1)
                )
                : 'AL',
            'terminal' => 'APT-01',
            'shift' => 'Shift Sore',
        ];

        // Ambil obat dari database
        $obats = Obat::where('is_active', true)->orderBy('nama_obat')->get();

        // Cari riwayat mutasi keluar nyata dari resep yang telah diselesaikan
        $completedPrescriptions = Booking::with(['patient', 'medicalRecord'])
            ->whereHas('medicalRecord', function ($q) {
                $q->where('plan->prescription_status', 'selesai');
            })
            ->latest('id')
            ->get();

        $drugs = $obats->map(function ($obat) use ($completedPrescriptions) {
            $rak = 'A-01';
            if (preg_match('/Rak:\s*([A-Za-z0-9\-]+)/i', $obat->deskripsi ?? '', $matches)) {
                $rak = strtoupper($matches[1]);
            }

            $history = [
                ['t' => 'in', 'time' => '08:00', 'd' => 'Hari ini', 'q' => (int) $obat->stok, 'p' => 'Stok Awal Gudang', 'r' => 'INIT-01'],
            ];

            // Tambahkan transaksi keluar hanya jika ada resep nyata yang selesai
            foreach ($completedPrescriptions as $booking) {
                $plan = $booking->medicalRecord?->plan ?? [];
                $meds = $plan['medications'] ?? [];
                foreach ($meds as $med) {
                    $match = false;
                    if (! empty($med['kode_obat']) && $med['kode_obat'] === $obat->kode_obat) {
                        $match = true;
                    } elseif (! empty($med['name']) && stripos($obat->nama_obat, trim(explode('—', $med['name'])[0])) !== false) {
                        $match = true;
                    }

                    if ($match) {
                        $qty = (int) ($med['quantity'] ?? $med['qty'] ?? 10);
                        $completedAt = $plan['completed_at'] ?? now()->format('Y-m-d H:i');
                        $timeStr = date('H:i', strtotime($completedAt));
                        $history[] = [
                            't' => 'out',
                            'time' => $timeStr,
                            'd' => 'Hari ini',
                            'q' => -$qty,
                            'p' => 'Pasien · '.($booking->patient?->full_name ?? 'Pasien'),
                            'r' => $booking->booking_code,
                        ];
                    }
                }
            }

            return [
                'id' => $obat->kode_obat,
                'name' => $obat->nama_obat,
                'desc' => $obat->satuan.' · '.($obat->deskripsi ?? 'Klinik Maristella'),
                'cat' => $obat->kategori,
                'rak' => $rak,
                'sys' => (int) $obat->stok,
                'act' => (int) $obat->stok,
                'price' => (float) $obat->harga,
                'h' => $history,
            ];
        })->values()->toArray();

        // Fallback default list if database is empty
        if (empty($drugs)) {
            $defaultHistoryInit = fn (int $qty) => [
                ['t' => 'in', 'time' => '08:00', 'd' => 'Hari ini', 'q' => $qty, 'p' => 'Stok Awal Gudang', 'r' => 'INIT-01'],
            ];

            $drugs = [
                [
                    'id' => 'PCT-500',
                    'name' => 'Paracetamol 500 mg',
                    'desc' => 'Tablet · strip isi 10',
                    'cat' => 'Analgesik',
                    'rak' => 'A-03',
                    'sys' => 128,
                    'act' => 128,
                    'price' => 12000,
                    'h' => $defaultHistoryInit(128),
                ],
                [
                    'id' => 'AMX-500',
                    'name' => 'Amoxicillin 500 mg',
                    'desc' => 'Kapsul · strip isi 10',
                    'cat' => 'Antibiotik',
                    'rak' => 'A-01',
                    'sys' => 64,
                    'act' => 64,
                    'price' => 18000,
                    'h' => $defaultHistoryInit(64),
                ],
                [
                    'id' => 'CTZ-010',
                    'name' => 'Cetirizine 10 mg',
                    'desc' => 'Tablet · strip isi 10',
                    'cat' => 'Antihistamin',
                    'rak' => 'A-04',
                    'sys' => 96,
                    'act' => 96,
                    'price' => 15000,
                    'h' => $defaultHistoryInit(96),
                ],
                [
                    'id' => 'OMP-020',
                    'name' => 'Omeprazole 20 mg',
                    'desc' => 'Kapsul · strip isi 10',
                    'cat' => 'Lambung',
                    'rak' => 'B-02',
                    'sys' => 36,
                    'act' => 36,
                    'price' => 25000,
                    'h' => $defaultHistoryInit(36),
                ],
                [
                    'id' => 'VTC-500',
                    'name' => 'Vitamin C 500 mg',
                    'desc' => 'Tablet · botol 30',
                    'cat' => 'Vitamin',
                    'rak' => 'B-05',
                    'sys' => 21,
                    'act' => 21,
                    'price' => 45000,
                    'h' => $defaultHistoryInit(21),
                ],
                [
                    'id' => 'MTF-500',
                    'name' => 'Metformin 500 mg',
                    'desc' => 'Tablet · strip isi 10',
                    'cat' => 'Antidiabetik',
                    'rak' => 'A-02',
                    'sys' => 18,
                    'act' => 18,
                    'price' => 22000,
                    'h' => $defaultHistoryInit(18),
                ],
                [
                    'id' => 'INS-GLA',
                    'name' => 'Insulin Glargine 100 IU/ml',
                    'desc' => 'Pen · 3 ml',
                    'cat' => 'Rak Dingin',
                    'rak' => 'KLK-1',
                    'sys' => 12,
                    'act' => 12,
                    'price' => 185000,
                    'h' => $defaultHistoryInit(12),
                ],
                [
                    'id' => 'AMB-SRP',
                    'name' => 'Ambroxol Sirup 15 mg/5 ml',
                    'desc' => 'Botol · 60 ml',
                    'cat' => 'Batuk & Flu',
                    'rak' => 'B-03',
                    'sys' => 40,
                    'act' => 40,
                    'price' => 16500,
                    'h' => $defaultHistoryInit(40),
                ],
            ];
        }

        // 3. Ambil antrean resep pasien yang memiliki rekam medis dengan obat
        $orders = Booking::with([
            'patient',
            'doctor.poli',
            'medicalRecord',
        ])
            ->whereHas('medicalRecord')
            ->latest('id')
            ->get()
            ->filter(function ($booking) {
                $meds = $booking->medicalRecord?->plan['medications'] ?? [];

                return is_array($meds) && count($meds) > 0;
            })
            ->values();

        $perluCount = $orders->filter(function ($o) {
            $st = $o->medicalRecord?->plan['prescription_status'] ?? 'perlu_diproses';

            return $st === 'perlu_diproses';
        })->count();

        $racikCount = $orders->filter(function ($o) {
            $st = $o->medicalRecord?->plan['prescription_status'] ?? 'perlu_diproses';

            return $st === 'sedang_diracik';
        })->count();

        $selesaiCount = $orders->filter(function ($o) {
            $st = $o->medicalRecord?->plan['prescription_status'] ?? 'perlu_diproses';

            return $st === 'selesai';
        })->count();

        $totalItemResep = $orders->sum(function ($booking) {
            $medications = $booking->medicalRecord?->plan['medications'] ?? [];

            return is_array($medications) ? count($medications) : 0;
        });

        return view('apoteker.dashboard', [
            'apoteker' => $apoteker,
            'drugs' => $drugs,
            'orders' => $orders,
            'stats' => [
                'total_drugs' => count($drugs),
                'total_orders' => $orders->count(),
                'perlu_count' => $perluCount,
                'racik_count' => $racikCount,
                'selesai_count' => $selesaiCount,
                'total_obat_resep' => $totalItemResep,
            ],
        ]);
    }

    /**
     * Tambah obat baru ke database.
     */
    public function storeObat(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'kode_obat' => ['required', 'string', 'max:50', 'unique:obats,kode_obat'],
            'nama_obat' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'string', 'max:100'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'rak' => ['nullable', 'string', 'max:50'],
            'stok' => ['required', 'integer', 'min:0'],
            'harga' => ['nullable', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ]);

        $deskripsi = ($validated['deskripsi'] ?? 'Kemasan standar').' · Rak: '.strtoupper($validated['rak'] ?? 'A-01');

        $obat = Obat::create([
            'kode_obat' => strtoupper($validated['kode_obat']),
            'nama_obat' => $validated['nama_obat'],
            'kategori' => $validated['kategori'],
            'satuan' => $validated['satuan'] ?? 'Pcs',
            'harga' => $validated['harga'] ?? 10000,
            'stok' => $validated['stok'],
            'deskripsi' => $deskripsi,
            'is_active' => true,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Obat '.$obat->nama_obat.' berhasil ditambahkan!',
                'obat' => $obat,
            ]);
        }

        return redirect()->route('apoteker.dashboard')
            ->with('success', 'Obat '.$obat->nama_obat.' berhasil ditambahkan ke inventaris!');
    }

    /**
     * Update stok aktual / stok opname.
     */
    public function updateStok(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kode_obat' => ['required', 'string', 'exists:obats,kode_obat'],
            'stok_aktual' => ['required', 'integer', 'min:0'],
        ]);

        $obat = Obat::where('kode_obat', $validated['kode_obat'])->firstOrFail();
        $selisih = $validated['stok_aktual'] - $obat->stok;

        return response()->json([
            'success' => true,
            'kode_obat' => $obat->kode_obat,
            'stok_sistem' => $obat->stok,
            'stok_aktual' => (int) $validated['stok_aktual'],
            'selisih' => $selisih,
            'message' => 'Stok aktual '.$obat->nama_obat.' berhasil dicatat.',
        ]);
    }

    /**
     * Selesaikan proses stok opname dan sinkronisasi ke sistem.
     */
    public function selesaikanOpname(Request $request): JsonResponse
    {
        $items = $request->input('items', []);

        if (is_array($items) && count($items) > 0) {
            foreach ($items as $item) {
                if (! empty($item['kode_obat']) && isset($item['stok_aktual'])) {
                    Obat::where('kode_obat', $item['kode_obat'])
                        ->update(['stok' => max(0, (int) $item['stok_aktual'])]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Stok Opname berhasil diselesaikan! Stok sistem telah diperbarui.',
        ]);
    }

    /**
     * ACC Resep Pasien (Status berubah menjadi 'sedang_diracik').
     */
    public function accResep(Request $request, int $bookingId): JsonResponse|RedirectResponse
    {
        $booking = Booking::with('medicalRecord', 'patient')->findOrFail($bookingId);
        $record = $booking->medicalRecord;

        if (! $record) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Rekam medis tidak ditemukan.'], 404);
            }

            return back()->with('error', 'Rekam medis tidak ditemukan.');
        }

        $plan = $record->plan ?? [];
        $plan['prescription_status'] = 'sedang_diracik';
        $plan['acc_at'] = now()->format('Y-m-d H:i');
        $plan['pharmacist_name'] = Auth::user()?->name ?? 'Apoteker';

        $record->plan = $plan;
        $record->save();

        $patientName = $booking->patient?->full_name ?? 'Pasien';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'sedang_diracik',
                'message' => "Resep pasien {$patientName} berhasil di-ACC! Obat sedang diracik.",
            ]);
        }

        return back()->with('success', "Resep pasien {$patientName} berhasil di-ACC! Obat sedang diracik.");
    }

    /**
     * Selesaikan & Serahkan Obat (Status berubah menjadi 'selesai', stok berkurang otomatis).
     */
    public function selesaiResep(Request $request, int $bookingId): JsonResponse|RedirectResponse
    {
        $booking = Booking::with('medicalRecord', 'patient')->findOrFail($bookingId);
        $record = $booking->medicalRecord;

        if (! $record) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Rekam medis tidak ditemukan.'], 404);
            }

            return back()->with('error', 'Rekam medis tidak ditemukan.');
        }

        $plan = $record->plan ?? [];
        $plan['prescription_status'] = 'selesai';
        $plan['completed_at'] = now()->format('Y-m-d H:i');
        $plan['pharmacist_name'] = Auth::user()?->name ?? 'Apoteker';

        $record->plan = $plan;
        $record->save();

        // Potong stok obat di database obats
        $medications = $plan['medications'] ?? [];
        $deductedItems = [];

        foreach ($medications as $med) {
            $obat = null;
            if (! empty($med['kode_obat'])) {
                $obat = Obat::where('kode_obat', $med['kode_obat'])->first();
            }
            if (! $obat && ! empty($med['name'])) {
                $cleanName = trim(explode('—', $med['name'])[0]);
                $cleanName = trim(explode('-', $cleanName)[0]);
                $cleanName = trim(explode('(', $cleanName)[0]);
                $obat = Obat::where('nama_obat', 'like', "%{$cleanName}%")->first();
            }

            if ($obat) {
                $qty = (int) ($med['quantity'] ?? $med['qty'] ?? 10);
                $oldStock = $obat->stok;
                $newStock = max(0, $oldStock - $qty);
                $obat->update(['stok' => $newStock]);
                $deductedItems[] = "{$obat->nama_obat} (-{$qty})";
            }
        }

        $patientName = $booking->patient?->full_name ?? 'Pasien';
        $deductMsg = count($deductedItems) > 0 ? ' Stok berkurang: '.implode(', ', $deductedItems) : '';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'selesai',
                'message' => "Obat untuk pasien {$patientName} selesai diracik dan diserahkan!{$deductMsg}",
            ]);
        }

        return back()->with('success', "Obat untuk pasien {$patientName} selesai diracik dan diserahkan!{$deductMsg}");
    }
}
