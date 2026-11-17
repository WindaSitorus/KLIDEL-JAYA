<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Payment;
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
}
