<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Obat;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DoctorController extends Controller
{
    /**
     * Resolve the current authenticated doctor model.
     */
    private function resolveCurrentDoctor(): ?Doctor
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();

        if (! $authUser) {
            return Doctor::with('poli')->first();
        }

        // 1. Direct relationship user_id
        $doctor = Doctor::with('poli')
            ->where('user_id', $authUser->id)
            ->first();

        // 2. Fallback matching doctor name
        if (! $doctor && ! empty($authUser->name)) {
            $cleanName = trim(str_replace(
                ['Dr. ', ', Sp.S', ', Sp.JP', ', Sp.OT'],
                '',
                $authUser->name
            ));

            $doctor = Doctor::with('poli')
                ->where('nama_dokter', 'like', '%'.$cleanName.'%')
                ->first();
        }

        // 3. Fallback matching email prefix
        if (! $doctor && ! empty($authUser->email)) {
            $prefix = explode('@', $authUser->email)[0];

            $doctor = Doctor::with('poli')
                ->where('nama_dokter', 'like', '%'.$prefix.'%')
                ->first();
        }

        return $doctor ?: Doctor::with('poli')->first();
    }

    /**
     * Get queues & patient cases STRICTLY for the specified doctor.
     * Enforces privacy: doctors can NEVER see other doctors' queues or patients!
     *
     * @return array<string, mixed>
     */
    private function getDoctorQueues(Doctor $doctor): array
    {
        $queues = [];

        if (Schema::hasTable('queues')) {
            $dbQueues = Queue::with([
                'booking.patient',
                'booking.doctor',
                'booking.medicalRecord',
            ])
                ->whereHas('booking', function ($q) use ($doctor) {
                    $q->where('doctor_id', $doctor->id);
                })
                ->orderBy('id', 'asc')
                ->get();

            foreach ($dbQueues as $dbQueue) {
                $qKey = 'Q-'.str_pad(
                    (string) $dbQueue->id,
                    3,
                    '0',
                    STR_PAD_LEFT
                );

                $booking = $dbQueue->booking;
                $patient = $booking?->patient;

                // STRICT PRIVACY RULE:
                // Doctor can ONLY see medical history examined by THIS doctor!
                $patientHistory = [];

                if ($patient) {
                    $pastRecords = MedicalRecord::whereHas(
                        'booking',
                        function ($q) use ($patient, $doctor) {
                            $q->where('patient_id', $patient->id)
                                ->where('doctor_id', $doctor->id)
                                ->where('status', 'completed');
                        }
                    )
                        ->with('booking.doctor')
                        ->latest('id')
                        ->get();

                    foreach ($pastRecords as $rec) {
                        $medsList = '';

                        if (
                            ! empty($rec->plan['medications'])
                            && is_array($rec->plan['medications'])
                        ) {
                            $medsList = collect(
                                $rec->plan['medications']
                            )->pluck('name')->implode(', ');
                        } elseif (! empty($rec->plan['clinical_action'])) {
                            $medsList = $rec->plan['clinical_action'];
                        }

                        $patientHistory[] = [
                            'date' => $rec->created_at
                                ? $rec->created_at->format('d M Y')
                                : now()->format('d M Y'),

                            'doctor' => $doctor->nama_dokter.' ('.
                                ($doctor->poli?->nama_poli ?? $doctor->spesialisasi).
                                ')',

                            'diagnosis' => $rec->assessmen['primary_diagnosis']
                                ?? 'Pemeriksaan Klinis',

                            'therapy' => $medsList
                                ?: 'Terapi simtomatik & observasi',
                        ];
                    }
                }

                $rec = $booking?->medicalRecord;
                $soap = null;

                if ($rec) {
                    $soap = [
                        'subjective' => $rec->subjective
                            ?? $booking?->symptoms
                            ?? '',

                        'objective' => $rec->objective ?? [],

                        'assessment' => $rec->assessmen ?? [],

                        'plan' => $rec->plan ?? [],
                    ];
                }

                $queues[$qKey] = [
                    'id' => $qKey,
                    'db_id' => $dbQueue->id,
                    'booking_id' => $booking?->id,
                    'queue_no' => $dbQueue->queue_number,
                    'time_slot' => $dbQueue->time_slot ?? '09:00 AM',
                    'status' => $dbQueue->status,

                    'patient' => [
                        'id' => 'P-'.($patient?->id ?? $dbQueue->id),
                        'mrn' => $patient?->mrn
                            ?? '#GM-0'.($patient?->id ?? $dbQueue->id),

                        'nik' => $patient?->nik
                            ?? '31710'.str_pad(
                                (string) $dbQueue->id,
                                11,
                                '0',
                                STR_PAD_LEFT
                            ),

                        'name' => $patient?->full_name ?? 'Pasien',

                        'initials' => strtoupper(
                            substr($patient?->full_name ?? 'P', 0, 1).
                            substr(
                                trim(
                                    strstr($patient?->full_name ?? ' P', ' ')
                                    ?: 'P'
                                ),
                                1,
                                1
                            )
                        ),

                        'age' => $patient?->dob
                            ? Carbon::parse($patient->dob)->age.' tahun'
                            : '32 tahun',

                        'dob' => $patient?->dob
                            ? Carbon::parse($patient->dob)->isoFormat('D MMMM Y')
                            : '12 Juni 1994',

                        'gender' => $patient?->gender ?? 'Perempuan',
                        'blood_type' => $patient?->blood_type ?? 'B+',
                        'phone' => $patient?->phone ?? '+62 812-9988-7766',
                        'address' => $patient?->address ?? 'Jakarta Selatan',
                        'allergies' => $patient?->allergies ?? [],
                        'past_illnesses' => $patient?->past_illnesses ?? [],
                        'regular_meds' => $patient?->regular_meds ?? [],
                    ],

                    'complaint' => [
                        'symptoms' => $booking?->symptoms ?? 'Konsultasi keluhan umum',
                        'duration' => $booking?->duration ?? '1 - 3 Hari',
                        'severity' => $booking?->severity ?? 'Sedang',
                        'notes' => $booking?->notes
                            ?? 'Pendaftaran konsultasi online',
                    ],

                    'soap' => $soap,
                    'history' => $patientHistory,
                ];
            }
        }

        return $queues;
    }

    /**
     * Display the Doctor Portal Dashboard (4 Use Cases).
     */
    public function index(Request $request): View
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();

        $doctorModel = $this->resolveCurrentDoctor();

        $doctor = [
            'id' => $doctorModel?->id ?? 1,

            'name' => $doctorModel?->nama_dokter
                ?? ($authUser?->name ?? 'Dr. Dokter Spesialis'),

            'specialty' => ($doctorModel?->poli?->nama_poli ?? 'Spesialis').
                ' ('.($doctorModel?->spesialisasi ?? 'Dokter').')',

            'hospital' => 'Klinik Maristella',

            'initials' => $doctorModel?->initials
                ?: strtoupper(substr($doctorModel?->nama_dokter ?? 'DR', 0, 2)),

            'email' => $authUser?->email
                ?? ($doctorModel?->user?->email ?? 'dokter@aihealth.com'),

            'role' => 'Dokter Spesialis',
            'image' => $doctorModel?->image
                ?: 'images/doctors/doctor-1.png',

            'is_doctor' => true,
        ];

        $queues = $this->getDoctorQueues($doctorModel);
        $hasRealQueues = ! empty($queues);

        if (! $hasRealQueues) {
            $queues['Q-NONE'] = [
                'id' => 'Q-NONE',
                'db_id' => 0,
                'booking_id' => null,
                'queue_no' => '00',
                'time_slot' => '-',
                'status' => 'waiting',

                'patient' => [
                    'id' => 'P-0',
                    'mrn' => '#GM-0000',
                    'nik' => '-',
                    'name' => 'Belum Ada Antrean',
                    'initials' => '--',
                    'age' => '-',
                    'dob' => '-',
                    'gender' => '-',
                    'blood_type' => '-',
                    'phone' => '-',
                    'address' => '-',
                    'allergies' => [],
                    'past_illnesses' => [],
                    'regular_meds' => [],
                ],

                'complaint' => [
                    'symptoms' => 'Belum ada antrean pasien untuk jadwal Anda hari ini.',
                    'duration' => '-',
                    'severity' => '-',
                    'notes' => '',
                ],

                'soap' => null,
                'history' => [],
            ];
        }

        // Selected active queue for examination
        $activeQueueId = $request->get('queue_id');

        if (! $activeQueueId || ! isset($queues[$activeQueueId])) {
            $waitingQueue = collect($queues)->firstWhere('status', 'waiting')
                ?: collect($queues)->firstWhere('status', 'in_progress');

            $activeQueueId = $waitingQueue['id']
                ?? (array_key_last($queues) ?: array_key_first($queues));
        }

        $activeQueue = $queues[$activeQueueId];

        // Summary Statistics
        $totalPatients = $hasRealQueues ? count($queues) : 0;

        $waitingCount = $hasRealQueues
            ? count(array_filter(
                $queues,
                fn ($q) => $q['status'] === 'waiting'
            ))
            : 0;

        $inProgressCount = $hasRealQueues
            ? count(array_filter(
                $queues,
                fn ($q) => $q['status'] === 'in_progress'
            ))
            : 0;

        $completedCount = $hasRealQueues
            ? count(array_filter(
                $queues,
                fn ($q) => $q['status'] === 'completed'
            ))
            : 0;

        return view('doctor.dashboard', [
            'doctor' => $doctor,
            'queues' => $queues,
            'activeQueue' => $activeQueue,
            'activeQueueId' => $activeQueueId,

            'stats' => [
                'total' => $totalPatients,
                'waiting' => $waitingCount,
                'in_progress' => $inProgressCount,
                'completed' => $completedCount,
            ],
        ]);
    }

    /**
     * Save SOAP Medical Record & Complete Examination
     * (Use Case 3: Isi Rekam Medis SOAP).
     */
    public function saveSoap(
        Request $request,
        string $queueId
    ): RedirectResponse|JsonResponse {
        $validated = $request->validate([
            'subjective' => ['required', 'string'],
            'bp' => ['nullable', 'string'],
            'hr' => ['nullable', 'string'],
            'rr' => ['nullable', 'string'],
            'temp' => ['nullable', 'string'],
            'spo2' => ['nullable', 'string'],
            'weight' => ['nullable', 'string'],
            'height' => ['nullable', 'string'],
            'physical_exam' => ['nullable', 'string'],
            'primary_diagnosis' => ['required', 'string'],
            'secondary_diagnosis' => ['nullable', 'string'],
            'clinical_notes' => ['nullable', 'string'],
            'therapy_plan' => ['nullable', 'string'],
            'medications_json' => ['nullable', 'string'],
            'return_date' => ['nullable', 'string'],
        ]);

        $doctorModel = $this->resolveCurrentDoctor();
        $queues = $this->getDoctorQueues($doctorModel);

        if (! isset($queues[$queueId])) {
            return back()->with(
                'error',
                'Data antrean tidak ditemukan atau bukan wewenang Anda.'
            );
        }

        // Parse medication lines or structured medications_json into structured array
        $meds = [];

        if ($request->filled('medications_json')) {
            $parsed = json_decode($request->input('medications_json'), true);
            if (is_array($parsed) && count($parsed) > 0) {
                foreach ($parsed as $p) {
                    $meds[] = [
                        'obat_id' => $p['obat_id'] ?? null,
                        'kode_obat' => $p['kode_obat'] ?? '',
                        'name' => $p['name'] ?? 'Obat',
                        'quantity' => (int) ($p['quantity'] ?? $p['qty'] ?? 10),
                        'dose' => $p['dose'] ?? '1 tablet',
                        'frequency' => $p['frequency'] ?? '3x1 sehari sesudah makan',
                        'notes' => $p['notes'] ?? '',
                    ];
                }
            }
        }

        if (empty($meds) && ! empty($validated['therapy_plan'])) {
            $lines = explode("\n", trim($validated['therapy_plan']));

            foreach ($lines as $line) {
                if (trim($line)) {
                    $cleanLine = trim($line);
                    $freq = '3x1 sehari sesudah makan';
                    $dose = '1 tablet';

                    if (preg_match('/(1x1|2x1|3x1|4x1[^\n\r\)]*)/i', $cleanLine, $fMatches)) {
                        $freq = trim($fMatches[1]);
                    }

                    $meds[] = [
                        'name' => $cleanLine,
                        'dose' => $dose,
                        'frequency' => $freq,
                        'quantity' => 10,
                        'notes' => '',
                    ];
                }
            }
        }

        $soapData = [
            'subjective' => $validated['subjective'],

            'objective' => [
                'bp' => $validated['bp'] ?? '120/80',
                'hr' => $validated['hr'] ?? '80',
                'rr' => $validated['rr'] ?? '18',
                'temp' => $validated['temp'] ?? '36.5',
                'spo2' => $validated['spo2'] ?? '99',
                'weight' => $validated['weight'] ?? '',
                'height' => $validated['height'] ?? '',
                'physical_exam' => $validated['physical_exam'] ?? 'Dalam batas normal.',
            ],

            'assessment' => [
                'primary_diagnosis' => $validated['primary_diagnosis'],
                'secondary_diagnosis' => $validated['secondary_diagnosis'] ?? '',
                'clinical_notes' => $validated['clinical_notes'] ?? '',
            ],

            'plan' => [
                'medications' => ! empty($meds)
                    ? $meds
                    : [
                        [
                            'name' => $validated['primary_diagnosis'].
                                ' Therapy Regimen',
                            'dose' => '1 unit',
                            'frequency' => 'Rutin',
                            'quantity' => 1,
                            'notes' => '',
                        ],
                    ],

                'clinical_action' => 'Pemeriksaan selesai dicatat oleh dokter.',

                'return_date' => $validated['return_date'] ?? '14 hari ke depan',

                'prescription_status' => 'perlu_diproses',
                'prescribed_at' => now()->toIso8601String(),
            ],
        ];

        // Update database booking & queue
        $booking = null;

        if (! empty($queues[$queueId]['booking_id'])) {
            $booking = Booking::with('patient')
                ->find($queues[$queueId]['booking_id']);
        }

        if (! $booking && session('current_booking_id')) {
            $booking = Booking::with('patient')
                ->find(session('current_booking_id'));
        }

        if ($booking) {
            MedicalRecord::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'subjective' => $validated['subjective'],
                    'objective' => $soapData['objective'],
                    'assessmen' => $soapData['assessment'],
                    'plan' => $soapData['plan'],
                ]
            );

            $booking->update([
                'status' => 'completed',
                'payment_status' => $booking->payment_status ?: 'paid',
            ]);

            Queue::where('booking_id', $booking->id)
                ->update(['status' => 'completed']);

            if (! empty($queues[$queueId]['db_id'])) {
                Queue::where('id', $queues[$queueId]['db_id'])
                    ->update(['status' => 'completed']);
            }

            session()->put('last_examined_booking_id', $booking->id);
            session()->put('last_examined_doctor_id', $doctorModel->id);
            session()->put(
                'last_examined_doctor_name',
                $doctorModel->nama_dokter
            );
            session()->put(
                'last_examined_patient',
                $booking->patient?->full_name
                    ?? $queues[$queueId]['patient']['name']
            );
            session()->put('last_examined_at', now()->toIso8601String());
            session()->forget('just_paid_booking_id');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rekam Medis SOAP berhasil disimpan untuk pasien '.
                    $queues[$queueId]['patient']['name'],
                'queue_id' => $queueId,
            ]);
        }

        return redirect()
            ->route('doctor.dashboard', ['queue_id' => $queueId])
            ->with(
                'success',
                'Rekam Medis SOAP untuk '.
                $queues[$queueId]['patient']['name'].
                ' berhasil disimpan dan status antrean diselesaikan.'
            );
    }

    /**
     * Search medicines from the existing obats table.
     * This endpoint is read-only for doctors.
     */
    public function searchObat(Request $request): JsonResponse
    {
        $search = trim($request->query('q', ''));

        // Require at least 2 characters
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $obats = Obat::query()
            ->where('is_active', true)
            ->where(function ($query) use ($search) {
                $query->where('nama_obat', 'like', "%{$search}%")
                    ->orWhere('kode_obat', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            })
            ->orderBy('nama_obat')
            ->limit(10)
            ->get([
                'id',
                'kode_obat',
                'nama_obat',
                'kategori',
                'satuan',
                'harga',
                'stok',
            ]);

        return response()->json($obats);
    }
}
