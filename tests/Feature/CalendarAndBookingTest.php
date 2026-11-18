<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarAndBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * 1. Test booking pada dokter, tanggal, dan jam yang sama harus ditolak (clash prevention).
     */
    public function test_booking_on_same_doctor_date_and_time_is_rejected(): void
    {
        $doctor = Doctor::first();
        // Use next Monday so practice schedule is always active
        $targetDate = Carbon::now()->next(Carbon::MONDAY)->toDateString();

        // Booking 1: 09:00 - 10:00
        $response1 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '10:00:00',
            'patient_name' => 'Pasien Pertama',
            'phone' => '081234567801',
            'symptoms' => 'Pemeriksaan rutin pertama',
        ]);

        $response1->assertOk()
            ->assertJsonPath('success', true);

        // Booking 2: Dokter yang sama, tanggal yang sama, jam yang sama persis
        $response2 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '10:00:00',
            'patient_name' => 'Pasien Kedua',
            'phone' => '081234567802',
            'symptoms' => 'Pemeriksaan kedua bentrok',
        ]);

        $response2->assertStatus(422)
            ->assertJsonValidationErrors(['jam_mulai']);

        $this->assertStringContainsString('Jadwal bentrok', $response2->json('errors.jam_mulai.0'));
    }

    /**
     * 2. Test booking dengan waktu yang saling bertabrakan (overlapping time) harus ditolak.
     */
    public function test_booking_with_overlapping_time_is_rejected(): void
    {
        $doctor = Doctor::first();
        $targetDate = Carbon::now()->next(Carbon::TUESDAY)->toDateString();

        // Booking 1: 09:00 - 10:30
        $response1 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '10:30:00',
            'patient_name' => 'Pasien Awal',
            'phone' => '081234567803',
            'symptoms' => 'Keluhan sesak napas',
        ]);
        $response1->assertOk();

        // Booking 2: 10:00 - 11:00 (Bertabrakan selama 30 menit antara 10:00 - 10:30)
        $response2 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '11:00:00',
            'patient_name' => 'Pasien Tabrakan',
            'phone' => '081234567804',
            'symptoms' => 'Keluhan berdebar',
        ]);

        $response2->assertStatus(422)
            ->assertJsonValidationErrors(['jam_mulai']);

        // Booking 3: 08:30 - 09:30 (Bertabrakan di awal antara 09:00 - 09:30)
        $response3 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '08:30:00',
            'jam_selesai' => '09:30:00',
            'patient_name' => 'Pasien Tabrakan Awal',
            'phone' => '081234567805',
            'symptoms' => 'Pemeriksaan tensi',
        ]);

        $response3->assertStatus(422)
            ->assertJsonValidationErrors(['jam_mulai']);
    }

    /**
     * 3. Test jadwal yang bersebelahan (adjacent) tetap boleh digunakan.
     * Misalnya booking pertama selesai pukul 09.00 dan booking berikutnya dimulai pukul 09.00.
     */
    public function test_adjacent_time_slots_are_allowed(): void
    {
        $doctor = Doctor::first();
        $targetDate = Carbon::now()->next(Carbon::WEDNESDAY)->toDateString();

        // Booking 1: 08:00 - 09:00
        $response1 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '09:00:00',
            'patient_name' => 'Pasien Sesi Pagi 1',
            'phone' => '081234567806',
            'symptoms' => 'Kontrol gula darah',
        ]);
        $response1->assertOk();

        // Booking 2: 09:00 - 10:00 (Tepat bersebelahan di 09:00)
        $response2 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '10:00:00',
            'patient_name' => 'Pasien Sesi Pagi 2',
            'phone' => '081234567807',
            'symptoms' => 'Kontrol tensi',
        ]);
        $response2->assertOk();

        // Booking 3: 10:00 - 11:00 (Tepat bersebelahan di 10:00)
        $response3 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '11:00:00',
            'patient_name' => 'Pasien Sesi Pagi 3',
            'phone' => '081234567808',
            'symptoms' => 'Konsultasi vitamin',
        ]);
        $response3->assertOk();
    }

    /**
     * 4. Test booking pada dokter yang berbeda di jam dan tanggal yang sama harus berhasil.
     */
    public function test_booking_on_different_doctors_at_same_time_succeeds(): void
    {
        $doctors = Doctor::take(2)->get();
        $this->assertCount(2, $doctors);

        $docA = $doctors[0];
        $docB = $doctors[1];
        $targetDate = Carbon::now()->next(Carbon::THURSDAY)->toDateString();

        // Booking untuk Dokter A di 09:00 - 10:00
        $resA = $this->postJson(route('booking.store'), [
            'doctor_id' => $docA->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '10:00:00',
            'patient_name' => 'Pasien Dokter A',
            'phone' => '081234567809',
            'symptoms' => 'Keluhan A',
        ]);
        $resA->assertOk();

        // Booking untuk Dokter B di 09:00 - 10:00 (Jam & Tanggal sama, Dokter beda)
        $resB = $this->postJson(route('booking.store'), [
            'doctor_id' => $docB->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '10:00:00',
            'patient_name' => 'Pasien Dokter B',
            'phone' => '081234567810',
            'symptoms' => 'Keluhan B',
        ]);
        $resB->assertOk();
    }

    /**
     * 5. Test booking pada tanggal yang berbeda di jam yang sama harus berhasil.
     */
    public function test_booking_on_different_dates_at_same_time_succeeds(): void
    {
        $doctor = Doctor::first();
        $date1 = Carbon::now()->next(Carbon::FRIDAY)->toDateString();
        $date2 = Carbon::now()->next(Carbon::SATURDAY)->toDateString();

        // Tanggal 1
        $res1 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $date1,
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '11:00:00',
            'patient_name' => 'Pasien Hari Jumat',
            'phone' => '081234567811',
            'symptoms' => 'Keluhan Jumat',
        ]);
        $res1->assertOk();

        // Tanggal 2
        $res2 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $date2,
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '11:00:00',
            'patient_name' => 'Pasien Hari Sabtu',
            'phone' => '081234567812',
            'symptoms' => 'Keluhan Sabtu',
        ]);
        $res2->assertOk();
    }

    /**
     * 6. Test booking setelah jadwal sebelumnya dibatalkan harus berhasil (slot kembali tersedia).
     */
    public function test_booking_after_previous_schedule_is_cancelled_succeeds(): void
    {
        $doctor = Doctor::first();
        $targetDate = Carbon::now()->next(Carbon::MONDAY)->toDateString();

        // Buat booking awal
        $res1 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '11:00:00',
            'jam_selesai' => '12:00:00',
            'patient_name' => 'Pasien Batal',
            'phone' => '081234567813',
            'symptoms' => 'Keluhan awal',
        ]);
        $res1->assertOk();

        $bookingId = $res1->json('booking.id');
        $booking = Booking::find($bookingId);

        // Batalkan booking via endpoint pembatalan
        $admin = User::where('role', 'admin')->first();
        $cancelRes = $this->actingAs($admin)->postJson(route('booking.cancel', $booking->id));
        $cancelRes->assertOk();

        $this->assertEquals('cancelled', $booking->fresh()->status);

        // Buat booking baru pada slot yang sama persis: harus BERHASIL
        $res2 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '11:00:00',
            'jam_selesai' => '12:00:00',
            'patient_name' => 'Pasien Baru Pengganti',
            'phone' => '081234567814',
            'symptoms' => 'Keluhan pengganti',
        ]);
        $res2->assertOk()
            ->assertJsonPath('success', true);
    }

    /**
     * 7. Test booking ketika kapasitas jadwal dokter sudah penuh harus ditolak.
     */
    public function test_booking_when_schedule_capacity_is_full_is_rejected(): void
    {
        $doctor = Doctor::first();
        $targetDate = Carbon::now()->next(Carbon::TUESDAY)->toDateString();

        // Atur kuota dokter khusus untuk pengujian ini menjadi 2 pasien
        Schedule::where('doctor_id', $doctor->id)->update(['kuota' => 2]);

        // Booking ke-1
        $res1 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '09:00:00',
            'patient_name' => 'Pasien Kuota 1',
            'phone' => '081234567815',
            'symptoms' => 'Pemeriksaan 1',
        ]);
        $res1->assertOk();

        // Booking ke-2
        $res2 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '10:00:00',
            'patient_name' => 'Pasien Kuota 2',
            'phone' => '081234567816',
            'symptoms' => 'Pemeriksaan 2',
        ]);
        $res2->assertOk();

        // Booking ke-3 pada hari yang sama (Kapasitas sudah 2/2) -> Harus DITOLAK
        $res3 = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $targetDate,
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '11:00:00',
            'patient_name' => 'Pasien Kuota 3 (Melebihi Kuota)',
            'phone' => '081234567817',
            'symptoms' => 'Pemeriksaan 3',
        ]);

        $responseStatus = $res3->status();
        $this->assertEquals(422, $responseStatus);
        $this->assertStringContainsString('Kapasitas', json_encode($res3->json()));
    }

    /**
     * 8. Test calendar data API endpoint returns real database data.
     */
    public function test_calendar_data_api_returns_database_records(): void
    {
        $doctor = Doctor::first();
        $targetDate = now()->toDateString();

        $response = $this->getJson(route('calendar.data', [
            'doctor_id' => $doctor->id,
            'date' => $targetDate,
        ]));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'date',
                'doctors',
                'bookings',
            ]);
    }

    /**
     * 9. Test calendar view loads and renders with real doctor tabs and view switcher.
     */
    public function test_calendar_view_loads_successfully(): void
    {
        $response = $this->get(route('calendar'));

        $response->assertOk();
        $response->assertSee('Kalender');
        $response->assertSee('Jadwal Praktik Dokter');
        $response->assertSee('Dr. Lina Wijaya');
        $response->assertSee('Harian');
        $response->assertSee('Mingguan');
        $response->assertSee('Bulanan');
    }

    /**
     * 10. Test booking pada tanggal masa lalu harus ditolak (past date blocking).
     */
    public function test_booking_on_past_date_is_rejected(): void
    {
        $doctor = Doctor::first();
        $pastDate = Carbon::yesterday()->toDateString();

        $response = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $pastDate,
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '10:00:00',
            'patient_name' => 'Pasien Kemarin',
            'phone' => '081234567899',
            'symptoms' => 'Batuk dan pilek',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['booking_date']);

        $this->assertStringContainsString('terlewati', $response->json('errors.booking_date.0'));
    }

    /**
     * 11. Test booking pada tanggal hari ini atau mendatang berhasil diterima.
     */
    public function test_booking_on_today_or_future_date_is_accepted(): void
    {
        $doctor = Doctor::first();
        $futureDate = Carbon::now()->addDays(2)->toDateString();

        $response = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $futureDate,
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '11:00:00',
            'patient_name' => 'Pasien Masa Depan',
            'phone' => '081234567888',
            'symptoms' => 'Konsultasi kesehatan umum',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('bookings', [
            'doctor_id' => $doctor->id,
            'jam_mulai' => '10:00:00',
        ]);

        $bookingId = $response->json('booking.id');
        $created = Booking::find($bookingId);
        $this->assertNotNull($created);
        $this->assertEquals($futureDate, Carbon::parse($created->booking_date)->toDateString());
    }

    /**
     * 12. Test booking pada jam sore dan malam (misal 05 PM / 17:00 dan 07 PM / 19:00) berhasil diterima.
     */
    public function test_booking_afternoon_and_evening_hours_is_accepted(): void
    {
        $doctor = Doctor::first();
        $targetDate = Carbon::now()->next(Carbon::WEDNESDAY)->toDateString();

        // Booking jam 17:00 (05 PM)
        $response = $this->postJson(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'booking_date' => $targetDate,
            'booking_time' => '05 PM',
            'jam_mulai' => '17:00:00',
            'jam_selesai' => '18:00:00',
            'patient_name' => 'Pasien Sore Hari',
            'phone' => '081234567777',
            'symptoms' => 'Pemeriksaan lanjutan sore hari',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $bookingId = $response->json('booking.id');
        $created = Booking::find($bookingId);
        $this->assertNotNull($created);
        $this->assertEquals('17:00:00', $created->jam_mulai);
        $this->assertEquals('18:00:00', $created->jam_selesai);
    }
}
