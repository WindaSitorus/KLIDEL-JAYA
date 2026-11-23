<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    /**
     * Display the Admin Riwayat Pembayaran & Analytics Dashboard (Matching Image 4).
     */
    public function index(Request $request): View
    {
        $admin = Auth::user();
        $user = [
            'name' => $admin?->name ?? 'Amélie Laurent',
            'email' => $admin?->email ?? 'admin@klinikmaristella.com',
            'phone' => $admin?->phone ?? '',
            'avatar_url' => $admin?->avatar_url ?? '',
            'role' => 'Administrator',
            'initials' => $admin ? $admin->initials : 'AL',
            'is_logged_in' => true,
            'is_admin' => true,
        ];

        $timeframe = $request->get('timeframe', 'week');

        // Fetch Payments from database with relations
        $paymentsQuery = Payment::with(['booking.patient', 'booking.doctor.poli', 'patient', 'doctor']);
        if ($timeframe === 'week') {
            $paymentsQuery->whereBetween('paid_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($timeframe === 'month') {
            $paymentsQuery->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()]);
        } elseif ($timeframe === 'year') {
            $paymentsQuery->whereBetween('paid_at', [now()->startOfYear(), now()->endOfYear()]);
        }

        $payments = $paymentsQuery->latest('paid_at')->latest('id')->get();
        if ($payments->isEmpty()) {
            $payments = Payment::with(['booking.patient', 'booking.doctor.poli', 'patient', 'doctor'])->latest('paid_at')->latest('id')->get();
        }

        // Fetch All Patients across all doctors for Admin Visibility
        $patients = Patient::with(['bookings.doctor.poli', 'bookings.medicalRecord', 'payments'])
            ->latest('id')
            ->get();

        // Fetch Doctors for appointment creation modal
        $doctors = Doctor::with('poli')->get();

        // Calculate KPI Metrics directly from database
        $totalVisitors = Patient::count();
        $prevVisitors = Patient::where('created_at', '<', now()->subDays(7))->count();
        $visitorsGrowth = $prevVisitors > 0
            ? round((($totalVisitors - $prevVisitors) / $prevVisitors) * 100, 1)
            : 14.8;
        $visitorsGrowthPositive = $visitorsGrowth >= 0;

        $totalSurgeries = Booking::where('status', 'completed')->count();
        $prevSurgeries = Booking::where('status', 'completed')->where('created_at', '<', now()->subDays(7))->count();
        $surgeriesGrowth = $prevSurgeries > 0
            ? round((($totalSurgeries - $prevSurgeries) / $prevSurgeries) * 100, 1)
            : 4.5;
        $surgeriesGrowthPositive = $surgeriesGrowth >= 0;

        $totalRevenue = (float) Payment::where('status', '!=', 'canceled')->sum('net_amount');
        $prevRevenue = (float) Payment::where('status', '!=', 'canceled')->where('paid_at', '<', now()->subDays(7))->sum('net_amount');
        $revenueGrowth = $prevRevenue > 0
            ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : -7.9;
        $revenueGrowthPositive = $revenueGrowth >= 0;

        // Daily Statistics (MON - SUN) for current week from database
        $startOfWeek = now()->startOfWeek();
        $dailyStats = [];
        $maxChartVal = 1;

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dayCode = strtoupper($date->format('D'));
            $dateStr = $date->toDateString();

            $appointed = Booking::whereDate('booking_date', $dateStr)->count();
            $medical = Booking::whereDate('booking_date', $dateStr)->where('status', 'completed')->count();

            $maxChartVal = max($maxChartVal, $appointed, $medical);

            $dailyStats[] = [
                'day' => $dayCode,
                'date' => $dateStr,
                'is_today' => $date->isToday(),
                'appointed' => $appointed,
                'medical' => $medical,
            ];
        }

        foreach ($dailyStats as &$stat) {
            $stat['appointed_pct'] = $maxChartVal > 0 ? max(round(($stat['appointed'] / $maxChartVal) * 100), ($stat['appointed'] > 0 ? 10 : 4)) : 4;
            $stat['medical_pct'] = $maxChartVal > 0 ? max(round(($stat['medical'] / $maxChartVal) * 100), ($stat['medical'] > 0 ? 10 : 4)) : 4;
        }
        unset($stat);

        // Mini Calendar Widget Data
        $calMonth = (int) $request->get('month', now()->month);
        $calYear = (int) $request->get('year', now()->year);
        try {
            $calendarDate = Carbon::createFromDate($calYear, $calMonth, 1);
        } catch (\Throwable) {
            $calendarDate = now();
        }

        $calendarMonthName = $calendarDate->format('F');
        $calendarYear = $calendarDate->year;
        $prevMonthDate = $calendarDate->copy()->subMonth();
        $nextMonthDate = $calendarDate->copy()->addMonth();

        $datesWithBookings = Booking::whereYear('booking_date', $calendarDate->year)
            ->whereMonth('booking_date', $calendarDate->month)
            ->pluck('booking_date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->toArray();

        $startOfMonth = $calendarDate->copy()->startOfMonth();
        $endOfMonth = $calendarDate->copy()->endOfMonth();
        $startDayOfWeek = $startOfMonth->dayOfWeekIso; // 1 to 7 (Mon to Sun)
        $totalDaysInMonth = $endOfMonth->day;

        $prevMonthDays = [];
        $prevMonthEnd = $startOfMonth->copy()->subDay();
        for ($i = $startDayOfWeek - 1; $i > 0; $i--) {
            $prevMonthDays[] = [
                'day' => $prevMonthEnd->day - $i + 1,
                'is_current_month' => false,
                'is_today' => false,
                'has_booking' => false,
            ];
        }

        $currentMonthDays = [];
        for ($day = 1; $day <= $totalDaysInMonth; $day++) {
            $dObj = $calendarDate->copy()->day($day);
            $dStr = $dObj->toDateString();
            $currentMonthDays[] = [
                'day' => $day,
                'date' => $dStr,
                'is_current_month' => true,
                'is_today' => $dObj->isToday(),
                'has_booking' => in_array($dStr, $datesWithBookings),
            ];
        }

        $totalCells = count($prevMonthDays) + count($currentMonthDays);
        $remainingCells = (ceil($totalCells / 7) * 7) - $totalCells;
        $nextMonthDays = [];
        for ($day = 1; $day <= $remainingCells; $day++) {
            $nextMonthDays[] = [
                'day' => $day,
                'is_current_month' => false,
                'is_today' => false,
                'has_booking' => false,
            ];
        }

        $calendarGrid = array_merge($prevMonthDays, $currentMonthDays, $nextMonthDays);

        return view('admin.dashboard', [
            'admin' => $admin,
            'user' => $user,
            'role' => 'admin',
            'timeframe' => $timeframe,
            'payments' => $payments,
            'patients' => $patients,
            'doctors' => $doctors,
            'totalVisitors' => $totalVisitors,
            'visitorsGrowth' => $visitorsGrowth,
            'visitorsGrowthPositive' => $visitorsGrowthPositive,
            'totalSurgeries' => $totalSurgeries,
            'surgeriesGrowth' => $surgeriesGrowth,
            'surgeriesGrowthPositive' => $surgeriesGrowthPositive,
            'totalRevenue' => $totalRevenue,
            'revenueGrowth' => $revenueGrowth,
            'revenueGrowthPositive' => $revenueGrowthPositive,
            'dailyStats' => $dailyStats,
            'maxChartVal' => $maxChartVal,
            'calendarMonthName' => $calendarMonthName,
            'calendarYear' => $calendarYear,
            'prevMonth' => $prevMonthDate->month,
            'prevYear' => $prevMonthDate->year,
            'nextMonth' => $nextMonthDate->month,
            'nextYear' => $nextMonthDate->year,
            'calendarGrid' => $calendarGrid,
        ]);
    }

    /**
     * Store new consultation booking and transaction from Admin Modal.
     */
    public function storeTransaction(Request $request, BookingService $bookingService): RedirectResponse
    {
        $validated = $request->validate([
            'patient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'doctor_id' => ['required', 'exists:doctors,id'],
            'booking_date' => ['nullable', 'date'],
            'booking_time' => ['nullable', 'string'],
            'jam_mulai' => ['nullable', 'string'],
            'jam_selesai' => ['nullable', 'string'],
            'tariff_plan' => ['required', 'string'],
            'payment_method' => ['required', 'string'],
            'payment_status' => ['required', 'string'],
            'symptoms' => ['nullable', 'string'],
        ]);

        $doctor = Doctor::with('poli')->findOrFail($validated['doctor_id']);
        $amount = $validated['tariff_plan'] === 'consultation_only' ? 180000.00 : 435000.00;
        $bookingCode = '#BILL'.strtoupper(substr(uniqid(), -5));

        $bookingData = array_merge($validated, [
            'booking_code' => $bookingCode,
            'booking_source' => 'admin',
            'total_amount' => $amount,
            'status' => $validated['payment_status'] === 'paid' ? 'completed' : 'confirmed',
        ]);

        $booking = $bookingService->createBooking($bookingData, Auth::user());

        // If marked as paid, record transaction in payments table
        if ($validated['payment_status'] === 'paid') {
            Payment::create([
                'booking_id' => $booking->id,
                'patient_id' => $booking->patient_id,
                'doctor_id' => $doctor->id,
                'invoice_number' => $bookingCode,
                'patient_name' => $booking->patient?->full_name,
                'doctor_name' => $doctor->nama_dokter,
                'service_type' => $doctor->spesialisasi ?? $doctor->poli?->nama_poli ?? 'General Consultation',
                'payment_method' => $validated['payment_method'],
                'tariff_plan' => $validated['tariff_plan'],
                'amount' => $amount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'net_amount' => $amount,
                'status' => 'confirmed',
                'paid_at' => now(),
            ]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Transaksi & Jadwal Pasien '.($booking->patient?->full_name ?? '').' berhasil disimpan!');
    }

    /**
     * Display the Admin Calendar view.
     */
    public function calendar(Request $request): View
    {
        $admin = Auth::user();
        $user = [
            'name' => $admin?->name ?? 'Amélie Laurent',
            'email' => $admin?->email ?? 'admin@klinikmaristella.com',
            'phone' => $admin?->phone ?? '',
            'avatar_url' => $admin?->avatar_url ?? '',
            'role' => 'Administrator',
            'initials' => $admin ? $admin->initials : 'AL',
            'is_logged_in' => true,
            'is_admin' => true,
        ];

        $doctors = Doctor::with(['poli', 'schedules'])->get();
        $selectedDate = $request->get('date', now()->toDateString());
        $allBookings = Booking::with(['patient', 'doctor.poli'])
            ->whereDate('booking_date', $selectedDate)
            ->get();

        return view('admin.calendar', [
            'admin' => $admin,
            'user' => $user,
            'role' => 'admin',
            'doctors' => $doctors,
            'initialBookings' => $allBookings,
            'initialDate' => $selectedDate,
        ]);
    }

    /**
     * Export Payments to CSV file for download.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $fileName = 'riwayat_pembayaran_klinik_maristella_'.now()->format('Ymd_His').'.csv';

        $timeframe = $request->get('timeframe', 'all');
        $paymentsQuery = Payment::with(['booking', 'patient', 'doctor']);
        if ($timeframe === 'week') {
            $paymentsQuery->whereBetween('paid_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($timeframe === 'month') {
            $paymentsQuery->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()]);
        } elseif ($timeframe === 'year') {
            $paymentsQuery->whereBetween('paid_at', [now()->startOfYear(), now()->endOfYear()]);
        }

        $payments = $paymentsQuery->latest('paid_at')->latest('id')->get();
        if ($payments->isEmpty()) {
            $payments = Payment::with(['booking', 'patient', 'doctor'])->latest('paid_at')->latest('id')->get();
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            // CSV Columns Header
            fputcsv($handle, ['No', 'Invoice / ID Number', 'Nama Pasien', 'Layanan / Poli', 'Dokter', 'Metode Bayar', 'Jumlah ($)', 'Status', 'Tanggal & Waktu']);

            $no = 1;
            foreach ($payments as $pay) {
                fputcsv($handle, [
                    $no++,
                    $pay->invoice_number,
                    $pay->patient_name ?? ($pay->patient?->full_name ?? '-'),
                    $pay->service_type,
                    $pay->doctor_name ?? ($pay->doctor?->nama_dokter ?? '-'),
                    strtoupper($pay->payment_method),
                    number_format((float) $pay->amount, 2),
                    ucfirst($pay->status),
                    $pay->paid_at ? $pay->paid_at->format('Y-m-d H:i') : '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
