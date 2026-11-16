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

        // Default mock history for drugs
        $defaultHistories = [
            'PCT-500' => [
                ['t' => 'in', 'time' => '08:42', 'd' => 'Hari ini', 'q' => 96, 'p' => 'PT Kimia Farma', 'r' => 'SJ-77120'],
                ['t' => 'out', 'time' => '11:15', 'd' => 'Hari ini', 'q' => -8, 'p' => 'Pasien · Budi S.', 'r' => 'RSP-4412'],
                ['t' => 'out', 'time' => '16:03', 'd' => 'Kemarin', 'q' => -12, 'p' => 'Pasien · Rina A.', 'r' => 'RSP-4398'],
            ],
            'AMX-500' => [
                ['t' => 'out', 'time' => '14:20', 'd' => 'Hari ini', 'q' => -10, 'p' => 'Pasien · Dewi L.', 'r' => 'RSP-4420'],
                ['t' => 'in', 'time' => '09:30', 'd' => 'Kemarin', 'q' => 50, 'p' => 'PT Kimia Farma', 'r' => 'SJ-77088'],
            ],
            'CTZ-010' => [
                ['t' => 'in', 'time' => '10:05', 'd' => 'Hari ini', 'q' => 48, 'p' => 'PT Enseval', 'r' => 'SJ-90311'],
                ['t' => 'out', 'time' => '13:40', 'd' => 'Hari ini', 'q' => -6, 'p' => 'Pasien · Agus P.', 'r' => 'RSP-4417'],
            ],
            'OMP-020' => [
                ['t' => 'out', 'time' => '15:12', 'd' => 'Hari ini', 'q' => -4, 'p' => 'Pasien · Sari M.', 'r' => 'RSP-4425'],
                ['t' => 'in', 'time' => '13:00', 'd' => 'Kemarin', 'q' => 24, 'p' => 'PT Mensa Binasukses', 'r' => 'SJ-55207'],
            ],
            'VTC-500' => [
                ['t' => 'out', 'time' => '12:30', 'd' => 'Hari ini', 'q' => -3, 'p' => 'Pasien · Toni W.', 'r' => 'RSP-4419'],
            ],
            'MTF-500' => [
                ['t' => 'in', 'time' => '09:15', 'd' => 'Hari ini', 'q' => 30, 'p' => 'PT Kimia Farma', 'r' => 'SJ-77121'],
                ['t' => 'out', 'time' => '17:45', 'd' => 'Hari ini', 'q' => -6, 'p' => 'Pasien · Hendra K.', 'r' => 'RSP-4428'],
            ],
            'INS-GLA' => [
                ['t' => 'in', 'time' => '08:10', 'd' => 'Hari ini', 'q' => 10, 'p' => 'PT Anugerah Pharmindo', 'r' => 'SJ-31044'],
                ['t' => 'out', 'time' => '10:50', 'd' => 'Hari ini', 'q' => -2, 'p' => 'Pasien · Lisa N.', 'r' => 'RSP-4414'],
            ],
            'AMB-SRP' => [
                ['t' => 'out', 'time' => '16:40', 'd' => 'Hari ini', 'q' => -5, 'p' => 'Pasien · Maya T.', 'r' => 'RSP-4427'],
                ['t' => 'in', 'time' => '11:20', 'd' => 'Kemarin', 'q' => 36, 'p' => 'PT Enseval', 'r' => 'SJ-90290'],
            ],
        ];

        // Ambil obat dari database
        $obats = Obat::where('is_active', true)->orderBy('nama_obat')->get();

        $drugs = $obats->map(function ($obat) use ($defaultHistories) {
            $rak = 'A-01';
            if (preg_match('/Rak:\s*([A-Za-z0-9\-]+)/i', $obat->deskripsi ?? '', $matches)) {
                $rak = strtoupper($matches[1]);
            }
            $history = $defaultHistories[$obat->kode_obat] ?? [
                ['t' => 'in', 'time' => '08:00', 'd' => 'Hari ini', 'q' => (int) $obat->stok, 'p' => 'Stok Awal Gudang', 'r' => 'INIT-01'],
            ];

            // For PCT-500 we simulate actual 116 vs system 128 as in design
            $act = $obat->kode_obat === 'PCT-500' ? 116 : (int) $obat->stok;

            return [
                'id' => $obat->kode_obat,
                'name' => $obat->nama_obat,
                'desc' => $obat->satuan.' · '.($obat->deskripsi ?? 'Klinik Maristella'),
                'cat' => $obat->kategori,
                'rak' => $rak,
                'sys' => (int) $obat->stok,
                'act' => $act,
                'price' => (float) $obat->harga,
                'h' => $history,
            ];
        })->values()->toArray();

        // Fallback default list if database is empty
        if (empty($drugs)) {
            $drugs = [
                [
                    'id' => 'PCT-500',
                    'name' => 'Paracetamol 500 mg',
                    'desc' => 'Tablet · strip isi 10',
                    'cat' => 'Analgesik',
                    'rak' => 'A-03',
                    'sys' => 128,
                    'act' => 116,
                    'price' => 12000,
                    'h' => $defaultHistories['PCT-500'],
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
                    'h' => $defaultHistories['AMX-500'],
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
                    'h' => $defaultHistories['CTZ-010'],
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
                    'h' => $defaultHistories['OMP-020'],
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
                    'h' => $defaultHistories['VTC-500'],
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
                    'h' => $defaultHistories['MTF-500'],
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
                    'h' => $defaultHistories['INS-GLA'],
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
                    'h' => $defaultHistories['AMB-SRP'],
                ],
            ];
        }

        // 3. Ambil antrean resep pasien yang sudah dibayar dan memiliki rekam medis
        $orders = Booking::with([
            'patient',
            'doctor.poli',
            'medicalRecord',
        ])
            ->where('payment_status', 'paid')
            ->whereHas('medicalRecord')
            ->latest('id')
            ->get();

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
}
