<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Poli;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    /**
     * Display the Klinik Maristella Home / Dashboard view.
     */
    public function index(): View
    {
        $authUser = auth()->user();
        $user = [
            'name' => $authUser?->name ?? 'Rina Anggraini',
            'email' => $authUser?->email ?? 'user@aihealth.com',
            'phone' => $authUser?->phone ?? '+62 812-5550-1920',
            'avatar_url' => $authUser?->avatar_url ?? '',
            'role' => $authUser ? ($authUser->isAdmin() ? 'Administrator' : ($authUser->isDoctor() ? 'Dokter Spesialis' : 'Pasien')) : 'Pasien',
            'initials' => $authUser ? $authUser->initials : 'RA',
            'is_logged_in' => auth()->check(),
            'is_admin' => $authUser?->isAdmin() ?? false,
            'is_doctor' => $authUser?->isDoctor() ?? false,
        ];

        // 1. Categories / Specialties from Database with Fallback
        $categories = [
            ['id' => 'all', 'name' => 'Semua'],
        ];

        if (Schema::hasTable('polis')) {
            $polis = Poli::all();
            if ($polis->isNotEmpty()) {
                foreach ($polis as $poli) {
                    $categories[] = [
                        'id' => $poli->slug,
                        'name' => $poli->name_en ?? $poli->nama_poli,
                    ];
                }
            }
        }

        if (count($categories) === 1) {
            $categories = [
                ['id' => 'all', 'name' => 'Semua'],
                ['id' => 'Cardiologist', 'name' => 'Heart'],
                ['id' => 'Neurologist', 'name' => 'Brain expert'],
                ['id' => 'Orthopedist', 'name' => 'Bone'],
            ];
        }

        // 2. Doctors from Database with Fallback
        $doctors = [];
        if (Schema::hasTable('doctors')) {
            $doctorModels = Doctor::with('poli')->get();
            if ($doctorModels->isNotEmpty()) {
                $doctors = $doctorModels->map(function (Doctor $doc): array {
                    return [
                        'id' => $doc->id,
                        'name' => $doc->nama_dokter,
                        'specialty' => $doc->spesialisasi ?: ($doc->poli?->nama_poli ?? 'Dokter Spesialis'),
                        'category' => $doc->poli?->slug ?? $doc->spesialisasi,
                        'rating' => (float) ($doc->rating ?: 4.8),
                        'price' => $doc->price ?: ('Rp '.number_format((float) $doc->price_num, 0, ',', '.')),
                        'price_num' => (float) $doc->price_num,
                        'initials' => $doc->initials ?: 'DR',
                        'image' => $doc->image ?: 'images/doctors/doctor-1.png',
                        'availability' => $doc->availability ?: '09:00 - 17:00 WIB',
                        'experience' => $doc->experience ?: '5 tahun',
                        'patients' => $doc->patients ?: '1.0k Up',
                    ];
                })->toArray();
            }
        }

        if (empty($doctors)) {
            $doctors = [
                [
                    'id' => 1,
                    'name' => 'Dr. Lina Wijaya',
                    'specialty' => 'Cardiologist',
                    'category' => 'Cardiologist',
                    'rating' => 4.9,
                    'price' => 'Rp 150.000',
                    'price_num' => 150000.00,
                    'initials' => 'LW',
                    'image' => 'images/doctors/doctor-1.png',
                    'availability' => '9AM - 8PM',
                    'experience' => '10 years',
                    'patients' => '3.8k Up',
                ],
                [
                    'id' => 2,
                    'name' => 'Dr. Anaya Kapoor',
                    'specialty' => 'Neurologist',
                    'category' => 'Neurologist',
                    'rating' => 4.8,
                    'price' => 'Rp 175.000',
                    'price_num' => 175000.00,
                    'initials' => 'AK',
                    'image' => 'images/doctors/doctor-2.png',
                    'availability' => '8AM - 6PM',
                    'experience' => '12 years',
                    'patients' => '4.6k Up',
                ],
                [
                    'id' => 3,
                    'name' => 'Dr. Saran Mitchell',
                    'specialty' => 'Orthopedist',
                    'category' => 'Orthopedist',
                    'rating' => 4.7,
                    'price' => 'Rp 180.000',
                    'price_num' => 180000.00,
                    'initials' => 'SM',
                    'image' => 'images/doctors/doctor-3.png',
                    'availability' => '10AM - 7PM',
                    'experience' => '15 years',
                    'patients' => '5.2k Up',
                ],
            ];
        }

        // 3. Resolve active booking and determine whether doctor has examined the patient
        $appointmentData = $this->resolveAppointmentState();
        $latestBooking = $appointmentData['booking'];
        $appointmentMode = $appointmentData['mode'];
        $isDoctorExamined = $appointmentData['doctor_examined'];

        $nextAppointment = [
            'doctor_name' => $latestBooking?->doctor?->nama_dokter ?? 'Dr. Saran Mitchell',
            'specialty' => $latestBooking?->doctor?->spesialisasi ?? 'Orthopedist',
            'date_time' => $latestBooking?->booking_time ?? 'Aug 14, 2026 · 2:30 PM',
            'initials' => $latestBooking?->doctor?->initials ?? 'SM',
            'image' => $latestBooking?->doctor?->image ?? 'images/doctors/doctor-3.png',
            'booking_code' => $latestBooking?->booking_code ?? '#BILL00124',
            'status' => $latestBooking?->status ?? ($isDoctorExamined ? 'completed' : 'confirmed'),
            'tariff_plan' => $latestBooking?->tariff_plan ?? 'with_medicine',
            'total_amount' => $latestBooking?->total_amount ?? 435000.00,
            'payment_status' => $latestBooking?->payment_status ?? 'unpaid',
        ];

        $bill = $this->resolveBillDetails($latestBooking, $nextAppointment);

        if ($latestBooking) {
            $nextAppointment['tariff_plan'] = $bill['tariff_plan'];
            $nextAppointment['total_amount'] = $bill['total_amount'];
        }

        $notifications = $this->resolveNotifications($latestBooking, $appointmentMode, $bill);

        // 4. Real-time booking dates for current week
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::SUNDAY);
        $bookingDays = [];
        $activeDayIndex = 0;

        for ($i = 0; $i < 7; $i++) {
            $dayCarbon = $startOfWeek->copy()->addDays($i);
            $isPast = $dayCarbon->lt($today);
            $isToday = $dayCarbon->isToday();

            if ($isToday) {
                $activeDayIndex = $i;
            }

            $bookingDays[] = [
                'name' => substr($dayCarbon->format('D'), 0, 2), // Su, Mo, Tu, We, Th, Fr, Sa
                'day_name' => $dayCarbon->format('D'),
                'date' => $dayCarbon->format('d'),
                'full_date' => $dayCarbon->format('Y-m-d'),
                'is_past' => $isPast,
                'is_today' => $isToday,
            ];
        }

        $currentHour = (int) Carbon::now()->format('H');
        $timeSlots = [
            ['time' => '08 AM', 'hour' => 8, 'start' => '08:00:00', 'end' => '09:00:00', 'period' => 'morning'],
            ['time' => '09 AM', 'hour' => 9, 'start' => '09:00:00', 'end' => '10:00:00', 'period' => 'morning'],
            ['time' => '10 AM', 'hour' => 10, 'start' => '10:00:00', 'end' => '11:00:00', 'period' => 'morning'],
            ['time' => '11 AM', 'hour' => 11, 'start' => '11:00:00', 'end' => '12:00:00', 'period' => 'morning'],
            ['time' => '12 PM', 'hour' => 12, 'start' => '12:00:00', 'end' => '13:00:00', 'period' => 'afternoon'],
            ['time' => '01 PM', 'hour' => 13, 'start' => '13:00:00', 'end' => '14:00:00', 'period' => 'afternoon'],
            ['time' => '02 PM', 'hour' => 14, 'start' => '14:00:00', 'end' => '15:00:00', 'period' => 'afternoon'],
            ['time' => '03 PM', 'hour' => 15, 'start' => '15:00:00', 'end' => '16:00:00', 'period' => 'afternoon'],
            ['time' => '04 PM', 'hour' => 16, 'start' => '16:00:00', 'end' => '17:00:00', 'period' => 'evening'],
            ['time' => '05 PM', 'hour' => 17, 'start' => '17:00:00', 'end' => '18:00:00', 'period' => 'evening'],
            ['time' => '06 PM', 'hour' => 18, 'start' => '18:00:00', 'end' => '19:00:00', 'period' => 'evening'],
            ['time' => '07 PM', 'hour' => 19, 'start' => '19:00:00', 'end' => '20:00:00', 'period' => 'evening'],
            ['time' => '08 PM', 'hour' => 20, 'start' => '20:00:00', 'end' => '21:00:00', 'period' => 'evening'],
        ];

        $currentMonthYear = Carbon::now()->translatedFormat('M Y');

        return view('home', [
            'user' => $user,
            'nextAppointment' => $nextAppointment,
            'bill' => $bill,
            'latestBooking' => $latestBooking,
            'appointmentMode' => $appointmentMode,
            'isDoctorExamined' => $isDoctorExamined,
            'categories' => $categories,
            'doctors' => $doctors,
            'notifications' => $notifications,
            'unreadCount' => count($notifications),
            'bookingDays' => $bookingDays,
            'activeDayIndex' => $activeDayIndex,
            'timeSlots' => $timeSlots,
            'currentHour' => $currentHour,
            'currentMonthYear' => $currentMonthYear,
        ]);
    }

    /**
     * Resolve the active appointment and determine whether doctor has examined the patient.
     *
     * @return array{booking: ?Booking, mode: string, doctor_examined: bool}
     */
    public function resolveAppointmentState(): array
    {
        if (! Schema::hasTable('bookings')) {
            return [
                'booking' => null,
                'mode' => 'welcome',
                'doctor_examined' => false,
            ];
        }

        $authUser = auth()->user();
        $sessionBookingId = session('current_booking_id');
        $lastExaminedBookingId = session('last_examined_booking_id');
        $justPaidBookingId = session('just_paid_booking_id');

        if ($justPaidBookingId) {
            $paidBooking = Booking::with(['doctor', 'patient', 'medicalRecord'])->find($justPaidBookingId)
                ?: Booking::with(['doctor', 'patient', 'medicalRecord'])->latest('id')->first();

            return [
                'booking' => $paidBooking,
                'mode' => 'welcome',
                'doctor_examined' => true,
            ];
        }

        // Check if logged-in user is a patient
        $patient = null;
        if (Schema::hasTable('patients') && $authUser) {
            $patient = Patient::where('user_id', $authUser->id)->latest('id')->first();
            if (! $patient && $authUser->role === 'patient') {
                $patient = Patient::where('full_name', $authUser->name)->latest('id')->first();
            }
        }

        $booking = null;

        // 1. Session booking takes highest precedence
        if ($sessionBookingId) {
            $booking = Booking::with(['doctor', 'patient', 'medicalRecord'])->find($sessionBookingId);
        }

        // 2. Doctor examined booking in session
        if (! $booking && $lastExaminedBookingId) {
            $booking = Booking::with(['doctor', 'patient', 'medicalRecord'])->find($lastExaminedBookingId);
        }

        // 3. For authenticated patient: look at THEIR latest booking
        if (! $booking && $patient) {
            $booking = Booking::with(['doctor', 'patient', 'medicalRecord'])
                ->where('patient_id', $patient->id)
                ->latest('id')
                ->first();
        }

        // 4. Global clinic state: look at the clinic's LATEST booking
        if (! $booking) {
            $booking = Booking::with(['doctor', 'patient', 'medicalRecord'])
                ->latest('id')
                ->first();
        }

        // Fallback to #BILL00124 if no bookings in DB
        if (! $booking) {
            $booking = Booking::with(['doctor', 'patient', 'medicalRecord'])
                ->where('booking_code', '#BILL00124')
                ->first();
        }

        // Determine State:
        // - 'welcome' : if payment is done (or just paid), or no active bill/upcoming booking
        // - 'after'   : if doctor has completed examination and bill is UNPAID (Gambar 1 / 2)
        // - 'before'  : if patient has upcoming booking waiting for doctor examination (Gambar 3)
        $doctorExamined = false;
        $mode = 'welcome';

        if ($booking) {
            $hasMedicalRecord = Schema::hasTable('medical_records') && $booking->medicalRecord()->exists();
            $isStatusCompleted = ($booking->status === 'completed');
            $doctorExamined = $isStatusCompleted || $hasMedicalRecord;

            $isPaid = ($booking->payment_status === 'paid')
                || (Schema::hasTable('payments') && (
                    $booking->payments()->where('status', 'confirmed')->exists()
                    || Payment::where('invoice_number', $booking->booking_code)->where('status', 'confirmed')->exists()
                ));

            if ($isPaid) {
                // If paid, show Welcome card ("Selamat datang, ada yang bisa dibantu?")
                $mode = 'welcome';
            } elseif ($doctorExamined && $booking->payment_status === 'unpaid') {
                // Doctor has examined and bill is unpaid -> show Tagihan card
                $mode = 'after';
            } elseif (in_array($booking->status, ['confirmed', 'in_progress']) && $booking->payment_status === 'unpaid') {
                // Booking created and waiting for doctor examination -> show Upcoming card (Gambar 3)
                $mode = 'before';
            } else {
                $mode = 'welcome';
            }
        }

        return [
            'booking' => $booking,
            'mode' => $mode,
            'doctor_examined' => $doctorExamined,
        ];
    }

    /**
     * Resolve the bill breakdown (consultation + prescribed medicines) for the patient view.
     *
     * @param  array<string, mixed>  $nextAppointment
     * @return array<string, mixed>
     */
    private function resolveBillDetails(?Booking $booking, array $nextAppointment = []): array
    {
        if ($booking) {
            return $booking->billDetails();
        }

        $totalAmount = (float) ($nextAppointment['total_amount'] ?? 0);

        return [
            'booking_id' => null,
            'booking_code' => (string) ($nextAppointment['booking_code'] ?? '-'),
            'doctor_name' => (string) ($nextAppointment['doctor_name'] ?? 'Dokter Pemeriksa'),
            'specialty' => (string) ($nextAppointment['specialty'] ?? ''),
            'tariff_plan' => 'consultation_only',
            'consultation_fee' => $totalAmount,
            'medicine_cost' => 0.0,
            'total_amount' => $totalAmount,
            'prescription_status' => null,
            'medications' => [],
        ];
    }

    /**
     * Resolve active real-time notifications for the patient.
     *
     * @return array<int, array{id: string, title: string, message: string, time: string, type: string, action_label: ?string, action_url: ?string, onclick: ?string}>
     */
    public function resolveNotifications(?Booking $latestBooking, string $appointmentMode, array $bill): array
    {
        $notifications = [];

        // 1. Tagihan siap dibayar (Doctor has examined)
        if ($appointmentMode === 'after' && $latestBooking && $latestBooking->payment_status === 'unpaid') {
            $formattedTotal = 'Rp '.number_format((float) $bill['total_amount'], 0, ',', '.');
            $doctorName = $latestBooking->doctor?->nama_dokter ?? 'Dokter Pemeriksa';
            $notifications[] = [
                'id' => 'notif-bill-'.$latestBooking->id,
                'title' => 'Tagihan Konsultasi Tersedia',
                'message' => "Pemeriksaan medis oleh {$doctorName} telah selesai. Tagihan sebesar {$formattedTotal} siap dibayar.",
                'time' => 'Baru saja',
                'type' => 'bill',
                'action_label' => 'Bayar Sekarang',
                'action_url' => null,
                'onclick' => 'window.openPaymentDrawer?.()',
            ];
        }

        // 2. Janji temu aktif menunggu konsultasi
        if ($appointmentMode === 'before' && $latestBooking && $latestBooking->payment_status === 'unpaid') {
            $doctorName = $latestBooking->doctor?->nama_dokter ?? 'Dokter Pemeriksa';
            $dateTime = $latestBooking->booking_time ?: ($latestBooking->booking_date ? date('d M Y', strtotime($latestBooking->booking_date)) : 'Hari Ini');
            $notifications[] = [
                'id' => 'notif-appt-'.$latestBooking->id,
                'title' => 'Janji Temu Menunggu',
                'message' => "Jadwal konsultasi Anda bersama {$doctorName} ({$latestBooking->doctor?->spesialisasi}) terdaftar untuk {$dateTime}.",
                'time' => 'Mendatang',
                'type' => 'appointment',
                'action_label' => 'Lihat Jadwal',
                'action_url' => route('calendar'),
                'onclick' => null,
            ];
        }

        // 3. Resep obat farmasi status
        if ($latestBooking && Schema::hasTable('medical_records') && $latestBooking->medicalRecord) {
            $plan = $latestBooking->medicalRecord->plan ?? [];
            $prescriptionStatus = is_array($plan) ? ($plan['prescription_status'] ?? null) : null;
            if ($prescriptionStatus === 'sedang_diracik') {
                $notifications[] = [
                    'id' => 'notif-rx-prep-'.$latestBooking->id,
                    'title' => 'Resep Sedang Diracik',
                    'message' => 'Apoteker sedang menyiapkan dan meracik resep obat Anda.',
                    'time' => 'Sedang berlangsung',
                    'type' => 'pharmacy',
                    'action_label' => null,
                    'action_url' => null,
                    'onclick' => null,
                ];
            } elseif ($prescriptionStatus === 'selesai') {
                $notifications[] = [
                    'id' => 'notif-rx-done-'.$latestBooking->id,
                    'title' => 'Obat Siap Diserahkan',
                    'message' => 'Obat resep Anda telah diserahkan oleh apoteker sesuai instruksi dosis.',
                    'time' => 'Selesai',
                    'type' => 'pharmacy',
                    'action_label' => null,
                    'action_url' => null,
                    'onclick' => null,
                ];
            }
        }

        // 4. Pembayaran berhasil / lunas
        if (session('just_paid_booking_id') || ($latestBooking && $latestBooking->payment_status === 'paid')) {
            $notifications[] = [
                'id' => 'notif-paid-'.($latestBooking?->id ?? 'last'),
                'title' => 'Pembayaran Berhasil',
                'message' => 'Transaksi pembayaran konsultasi Anda telah diverifikasi dan lunas.',
                'time' => 'Lunas',
                'type' => 'success',
                'action_label' => null,
                'action_url' => null,
                'onclick' => null,
            ];
        }

        return $notifications;
    }

    /**
     * Get real-time appointment and examination status (for automatic client trigger).
     */
    public function getAppointmentStatus(): JsonResponse
    {
        $appointmentData = $this->resolveAppointmentState();
        $latestBooking = $appointmentData['booking'];
        $bill = $this->resolveBillDetails($latestBooking);
        $notifications = $this->resolveNotifications($latestBooking, $appointmentData['mode'], $bill);

        return response()->json([
            'success' => true,
            'mode' => $appointmentData['mode'],
            'doctor_examined' => $appointmentData['doctor_examined'],
            'status' => $latestBooking?->status ?? 'confirmed',
            'booking_id' => $latestBooking?->id,
            'booking_code' => $latestBooking?->booking_code ?? '#BILL00124',
            'doctor_name' => $latestBooking?->doctor?->nama_dokter ?? 'Dr. Saran Mitchell',
            'specialty' => $latestBooking?->doctor?->spesialisasi ?? 'Orthopedist',
            'image' => $latestBooking?->doctor?->image ?? 'images/doctors/doctor-3.png',
            'total_amount' => $latestBooking ? $bill['total_amount'] : 435000.00,
            'payment_status' => $latestBooking?->payment_status ?? 'unpaid',
            'tariff_plan' => $latestBooking ? $bill['tariff_plan'] : 'with_medicine',
            'bill' => $bill,
            'notifications' => $notifications,
            'unread_count' => count($notifications),
        ]);
    }

    /**
     * Process patient payment and save to database.
     */
    public function processPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => ['nullable', 'integer'],
            'invoice_number' => ['nullable', 'string'],
            'tariff_plan' => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
            'amount' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);

        $booking = null;
        if (! empty($validated['booking_id']) && Schema::hasTable('bookings')) {
            $booking = Booking::with(['patient', 'doctor'])->find($validated['booking_id']);
        }

        if (! $booking && ! empty($validated['invoice_number']) && Schema::hasTable('bookings')) {
            $booking = Booking::with(['patient', 'doctor'])->where('booking_code', $validated['invoice_number'])->first();
        }

        if (! $booking && session('last_examined_booking_id') && Schema::hasTable('bookings')) {
            $booking = Booking::with(['patient', 'doctor'])->find(session('last_examined_booking_id'));
        }

        if (! $booking && session('current_booking_id') && Schema::hasTable('bookings')) {
            $booking = Booking::with(['patient', 'doctor'])->find(session('current_booking_id'));
        }

        if (! $booking && Schema::hasTable('bookings')) {
            $booking = Booking::with(['patient', 'doctor'])
                ->where('status', 'completed')
                ->where('payment_status', 'unpaid')
                ->latest('id')
                ->first();
        }

        if (! $booking && Schema::hasTable('bookings')) {
            $booking = Booking::with(['patient', 'doctor'])->latest('id')->first();
        }

        $amount = (float) ($validated['amount'] ?? ($booking?->total_amount ?? 435000.00));
        $tariffPlan = $validated['tariff_plan'] ?? ($booking?->tariff_plan ?? 'with_medicine');
        $paymentMethod = $validated['payment_method'] ?? 'cash';
        $invoiceNumber = $validated['invoice_number'] ?? ($booking?->booking_code ?? '#BILL00124');

        $payment = null;
        if (Schema::hasTable('payments')) {
            $payment = Payment::create([
                'booking_id' => $booking?->id,
                'patient_id' => $booking?->patient_id,
                'doctor_id' => $booking?->doctor_id,
                'invoice_number' => $invoiceNumber,
                'patient_name' => $booking?->patient?->full_name ?? auth()->user()?->name ?? 'Rina Anggraini',
                'doctor_name' => $booking?->doctor?->nama_dokter ?? 'Dr. Saran Mitchell',
                'service_type' => $booking?->doctor?->spesialisasi ?? 'Orthopedics',
                'payment_method' => $paymentMethod,
                'tariff_plan' => $tariffPlan,
                'amount' => $amount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'net_amount' => $amount,
                'status' => 'confirmed',
                'notes' => $validated['notes'] ?? 'Pembayaran tagihan via portal pasien',
                'paid_at' => now(),
            ]);
        }

        if ($booking) {
            $booking->update([
                'payment_status' => 'paid',
                'status' => 'completed',
                'total_amount' => $amount,
                'tariff_plan' => $tariffPlan,
            ]);
        }

        session(['just_paid_booking_id' => $booking?->id ?? 1]);
        session()->forget('last_examined_booking_id');
        session()->forget('current_booking_id');

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil disimpan ke database!',
            'payment' => $payment,
            'invoice_number' => $invoiceNumber,
            'amount' => $amount,
            'mode' => 'welcome',
        ]);
    }

    /**
     * Store a newly created consultation booking in database.
     */
    public function storeBooking(Request $request, BookingService $bookingService): JsonResponse
    {
        $validated = $request->validate([
            'patient_name' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'symptoms' => ['required', 'string'],
            'duration' => ['nullable', 'string'],
            'severity' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'doctor_id' => ['nullable', 'integer'],
            'booking_date' => ['nullable', 'date', 'after_or_equal:today'],
            'booking_time' => ['nullable', 'string'],
            'jam_mulai' => ['nullable', 'string'],
            'jam_selesai' => ['nullable', 'string'],
        ], [
            'booking_date.after_or_equal' => 'Tanggal booking tidak boleh di masa lalu (sudah terlewati).',
            'booking_date.date' => 'Format tanggal booking tidak valid.',
        ]);

        $booking = $bookingService->createBooking($validated, auth()->user());

        session(['current_booking_id' => $booking->id]);
        session()->forget('last_examined_booking_id');
        session()->forget('just_paid_booking_id');

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran Berhasil! Sesi dengan '.($booking->doctor?->nama_dokter ?? 'Dokter').' telah dijadwalkan.',
            'booking' => $booking,
            'queue' => $booking->queue,
        ]);
    }

    /**
     * Cancel an existing consultation booking.
     */
    public function cancelBooking(Request $request, Booking $booking, BookingService $bookingService): JsonResponse
    {
        $cancelledBooking = $bookingService->cancelBooking($booking, auth()->user());

        return response()->json([
            'success' => true,
            'message' => 'Jadwal booking berhasil dibatalkan. Slot jam kini kembali kosong.',
            'booking' => $cancelledBooking,
        ]);
    }

    /**
     * Return JSON data of doctors, schedules, and bookings for interactive calendar.
     */
    public function calendarData(Request $request, BookingService $bookingService): JsonResponse
    {
        $doctorId = $request->has('doctor_id') && $request->get('doctor_id') !== 'all' && is_numeric($request->get('doctor_id'))
            ? (int) $request->get('doctor_id')
            : null;
        $date = $request->get('date', now()->toDateString());

        $data = $bookingService->getCalendarData($doctorId, $date);

        return response()->json([
            'success' => true,
            'date' => $data['date'],
            'doctors' => $data['doctors'],
            'bookings' => $data['bookings'],
        ]);
    }

    /**
     * Display the Patient Calendar view.
     */
    public function calendar(Request $request): View
    {
        $authUser = auth()->user();
        $user = [
            'name' => $authUser?->name ?? 'Rina Anggraini',
            'email' => $authUser?->email ?? 'rina@example.com',
            'phone' => $authUser?->phone ?? '+62 812-5550-1920',
            'avatar_url' => $authUser?->avatar_url ?? '',
            'role' => $authUser ? ($authUser->isAdmin() ? 'Administrator' : 'Pasien') : 'Pasien',
            'initials' => $authUser ? $authUser->initials : 'RA',
            'is_logged_in' => auth()->check(),
            'is_admin' => $authUser?->isAdmin() ?? false,
        ];

        $doctors = Doctor::with(['poli', 'schedules'])->get();
        $selectedDate = $request->get('date', now()->toDateString());
        $allBookings = Booking::with(['patient', 'doctor.poli'])
            ->whereDate('booking_date', $selectedDate)
            ->get();

        return view('admin.calendar', [
            'user' => $user,
            'role' => 'patient',
            'doctors' => $doctors,
            'initialBookings' => $allBookings,
            'initialDate' => $selectedDate,
        ]);
    }

    /**
     * Display the Patient Medical Records (Rekam Medis) view.
     */
    public function medicalRecords(): View
    {
        $authUser = auth()->user();
        $user = [
            'name' => $authUser?->name ?? 'Rina Anggraini',
            'email' => $authUser?->email ?? 'rina.anggraini@aihealth.com',
            'phone' => '+62 812 5550 1920',
            'dob' => '12-06-1992 (34 tahun)',
            'gender' => 'Perempuan',
            'mrn' => '#GM-0214',
            'address' => 'Jl. Senopati No. 42, Kebayoran Baru, Jakarta Selatan, 12190',
            'role' => $authUser ? ($authUser->isAdmin() ? 'Administrator' : 'Pasien') : 'Pasien',
            'initials' => $authUser ? strtoupper(substr($authUser->name, 0, 1).substr(trim(strstr($authUser->name, ' ') ?: ''), 0, 1)) : 'RA',
            'is_logged_in' => auth()->check(),
            'is_admin' => $authUser?->isAdmin() ?? false,
        ];

        return view('rekam-medis', [
            'user' => $user,
        ]);
    }
}
