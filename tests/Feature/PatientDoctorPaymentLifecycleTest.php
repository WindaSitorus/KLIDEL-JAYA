<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Obat;
use App\Models\Payment;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientDoctorPaymentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * Test the full 5-step clinic lifecycle:
     * 1. Pasien request (Booking)
     * 2. Dokter periksa (In doctor portal)
     * 3. Dokter sudah memeriksa (SOAP saved)
     * 4. Pasien mendapat bill tagihan (Status 'after' with doctor details & amount)
     * 5. Pasien selesai membayar (Status 'welcome', recorded in DB & Admin)
     */
    public function test_complete_five_step_lifecycle_works_end_to_end(): void
    {
        // -------------------------------------------------------------
        // STEP 1: Pasien request (Booking consultation)
        // -------------------------------------------------------------
        $doctor = Doctor::where('nama_dokter', 'like', '%Anaya%')->first();
        $this->assertNotNull($doctor);

        $bookingResponse = $this->postJson('/booking', [
            'patient_name' => 'Budi Lifecycle Test',
            'phone' => '0812-3344-9999',
            'symptoms' => 'Nyeri migrain kepala sebelah kiri dan vertigo',
            'duration' => '1 - 3 Hari',
            'severity' => 'Sedang',
            'doctor_id' => $doctor->id,
            'booking_time' => '10:00 AM',
        ]);

        $bookingResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $bookingId = $bookingResponse->json('booking.id');
        $bookingCode = $bookingResponse->json('booking.booking_code');
        $this->assertNotNull($bookingId);

        // Verification: Appointment status on home page should be 'before' (Upcoming)
        $homeStatus = $this->getJson('/appointment-status');
        $homeStatus->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('mode', 'before')
            ->assertJsonPath('doctor_name', $doctor->nama_dokter)
            ->assertJsonPath('payment_status', 'unpaid');

        // -------------------------------------------------------------
        // STEP 2: Dokter periksa (In doctor portal)
        // -------------------------------------------------------------
        $doctorUser = User::where('email', 'anaya@aihealth.com')->first();
        $this->assertNotNull($doctorUser);

        $doctorDashboard = $this->actingAs($doctorUser)->get('/doctor');
        $doctorDashboard->assertStatus(200)
            ->assertSee('Budi Lifecycle Test')
            ->assertSee('Nyeri migrain');

        // -------------------------------------------------------------
        // STEP 3: Dokter sudah memeriksa (Save SOAP and complete examination)
        // -------------------------------------------------------------
        // Find the active queue key in dashboard view
        $queues = $doctorDashboard->viewData('queues');
        $targetQueue = collect($queues)->firstWhere('patient.name', 'Budi Lifecycle Test');
        $this->assertNotNull($targetQueue, 'Patient Budi Lifecycle Test should appear in Dr. Anaya queue');

        $soapResponse = $this->actingAs($doctorUser)->postJson("/doctor/soap/{$targetQueue['id']}", [
            'subjective' => 'Pasien mengeluh nyeri kepala sebelah kiri berdenyut disertai mual.',
            'primary_diagnosis' => 'G43.0 - Migraine without aura',
            'therapy_plan' => "Sumatriptan 50mg — 1 tablet saat serangan\nParacetamol 500mg — 3x1 tablet",
            'return_date' => '14 hari ke depan',
        ]);

        $soapResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        // -------------------------------------------------------------
        // STEP 4: Pasien mendapat bill tagihan (Card switches to 'after' UNPAID)
        // -------------------------------------------------------------
        // Even unauthenticated or cross-session, home page status must return 'after'
        $billStatus = $this->getJson('/appointment-status');
        $billStatus->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('mode', 'after')
            ->assertJsonPath('doctor_examined', true)
            ->assertJsonPath('booking_code', $bookingCode)
            ->assertJsonPath('doctor_name', $doctor->nama_dokter)
            ->assertJsonPath('payment_status', 'unpaid')
            ->assertJsonPath('total_amount', 475000);

        // -------------------------------------------------------------
        // STEP 5: Pasien selesai membayar (Processes payment & switches to 'welcome')
        // -------------------------------------------------------------
        $paymentResponse = $this->postJson('/payment', [
            'booking_id' => $bookingId,
            'invoice_number' => $bookingCode,
            'tariff_plan' => 'with_medicine',
            'amount' => 475000.00,
            'payment_method' => 'cash',
            'notes' => 'Pembayaran tunai pasien di kasir',
        ]);

        $paymentResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('mode', 'welcome');

        // Verification: Home page returns to 'welcome' card
        $welcomeStatus = $this->getJson('/appointment-status');
        $welcomeStatus->assertStatus(200)
            ->assertJsonPath('mode', 'welcome');

        // Verification: Payment recorded in database
        $this->assertDatabaseHas('payments', [
            'invoice_number' => $bookingCode,
            'amount' => 475000.00,
            'status' => 'confirmed',
        ]);

        // Verification: Payment appears on Admin Dashboard
        $adminUser = User::where('email', 'admin@aihealth.com')->first();
        $adminDashboard = $this->actingAs($adminUser)->get('/admin');
        $adminDashboard->assertStatus(200)
            ->assertSee($bookingCode)
            ->assertSee('Budi Lifecycle Test');
    }

    /**
     * Test the explicit 6-step clinic workflow requested by the user:
     * 1. Pasien mengajukan permintaan pertemuan
     * 2. Dokter menerima permintaan dan memeriksa pasien
     * 3. Dokter membuat ACC pemeriksaan
     * 4. Rikues daftar obat masuk ke apoteker
     * 5. Apoteker membuat obat (ACC racik & selesaikan hingga stok berkurang)
     * 6. Semua hasil yang sudah dilakukan masuk ke administrator
     */
    public function test_complete_six_step_clinic_workflow_from_patient_to_admin(): void
    {
        // -------------------------------------------------------------
        // STEP 1: Pasien mengajukan permintaan pertemuan
        // -------------------------------------------------------------
        $doctor = Doctor::first();
        $this->assertNotNull($doctor);

        $bookingRes = $this->postJson('/booking', [
            'patient_name' => 'Dewi workflow Test',
            'phone' => '0813-8899-7711',
            'symptoms' => 'Batuk berdahak dan demam tinggi',
            'duration' => '1 - 3 Hari',
            'severity' => 'Sedang',
            'doctor_id' => $doctor->id,
            'booking_time' => '14:00',
        ]);

        $bookingRes->assertOk()->assertJsonPath('success', true);
        $bookingId = $bookingRes->json('booking.id');
        $bookingCode = $bookingRes->json('booking.booking_code');
        $this->assertNotNull($bookingId);

        $queue = Queue::where('booking_id', $bookingId)->first();
        $this->assertNotNull($queue);
        $this->assertSame('waiting', $queue->status);

        // -------------------------------------------------------------
        // STEP 2: Dokter menerima permintaan dan memeriksa pasien
        // -------------------------------------------------------------
        $doctorUser = User::where('role', 'doctor')->first();
        $this->assertNotNull($doctorUser);

        $queueKey = 'Q-'.str_pad((string) $queue->id, 3, '0', STR_PAD_LEFT);

        $terimaRes = $this->actingAs($doctorUser)->postJson(route('doctor.terimaPeriksa', $queueKey));
        $terimaRes->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', 'in_progress');

        $queue->refresh();
        $this->assertSame('in_progress', $queue->status);

        // -------------------------------------------------------------
        // STEP 3: Dokter membuat ACC pemeriksaan
        // -------------------------------------------------------------
        $obat = Obat::where('kode_obat', 'PCT-500')->first();
        $this->assertNotNull($obat);
        $initialStock = $obat->stok;

        $medications = [
            [
                'obat_id' => $obat->id,
                'kode_obat' => $obat->kode_obat,
                'name' => $obat->nama_obat,
                'quantity' => 5,
                'satuan' => 'Tablet',
                'dose' => '1 tablet',
                'frequency' => '3x1 sesudah makan',
                'harga' => 12000,
            ],
        ];

        $soapRes = $this->actingAs($doctorUser)->postJson(route('doctor.saveSoap', $queueKey), [
            'subjective' => 'Pasien batuk berdahak dan demam tinggi sejak 2 hari yang lalu.',
            'bp' => '115/75',
            'hr' => '82',
            'rr' => '18',
            'temp' => '38.5',
            'spo2' => '98',
            'primary_diagnosis' => 'J06.9 - Infeksi Saluran Pernapasan Akut',
            'therapy_plan' => 'Paracetamol 500 mg 3x1 tablet sesudah makan bila demam',
            'medications_json' => json_encode($medications),
            'return_date' => '5 hari lagi',
        ]);

        $soapRes->assertOk()->assertJsonPath('success', true);

        $booking = Booking::with('medicalRecord')->find($bookingId);
        $this->assertSame('completed', $booking->status);
        $this->assertNotNull($booking->medicalRecord);
        $this->assertTrue($booking->medicalRecord->plan['doctor_acc'] ?? false);
        $this->assertSame('perlu_diproses', $booking->medicalRecord->plan['prescription_status']);

        // -------------------------------------------------------------
        // STEP 4: Rikues daftar obat masuk ke apoteker
        // -------------------------------------------------------------
        $apotekerUser = User::where('email', 'apoteker@aihealth.com')->first();
        $this->assertNotNull($apotekerUser);

        $apotekerDashboard = $this->actingAs($apotekerUser)->get(route('apoteker.dashboard'));
        $apotekerDashboard->assertOk()
            ->assertSee('Dewi workflow Test')
            ->assertSee($bookingCode)
            ->assertSee('PERLU DIPROSES');

        // -------------------------------------------------------------
        // STEP 5: Apoteker membuat obat (ACC racik lalu selesaikan)
        // -------------------------------------------------------------
        // 5a. ACC resep -> sedang_diracik
        $accResepRes = $this->actingAs($apotekerUser)->postJson(route('apoteker.resep.acc', $bookingId));
        $accResepRes->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', 'sedang_diracik');

        // 5b. Selesaikan pembuatan obat -> selesai & potong stok
        $selesaiResepRes = $this->actingAs($apotekerUser)->postJson(route('apoteker.resep.selesai', $bookingId));
        $selesaiResepRes->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', 'selesai');

        // Verify stock deducted
        $obat->refresh();
        $this->assertSame($initialStock - 5, $obat->stok);

        // -------------------------------------------------------------
        // STEP 6: Semua hasil yang sudah dilakukan masuk ke administrator
        // -------------------------------------------------------------
        $adminUser = User::where('email', 'admin@aihealth.com')->first();
        $this->assertNotNull($adminUser);

        $adminDashboard = $this->actingAs($adminUser)->get(route('admin.dashboard'));
        $adminDashboard->assertOk()
            ->assertSee('Dewi workflow Test')
            ->assertSee($bookingCode)
            ->assertSee('Semua Hasil Pemeriksaan')
            ->assertSee('Infeksi Saluran Pernapasan Akut')
            ->assertSee('Obat Selesai Dibuat');
    }
}
