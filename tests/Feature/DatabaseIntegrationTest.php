<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Poli;
use App\Models\Queue;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the database seeder populates all clinic domain models and relations properly.
     */
    public function test_database_seeder_populates_all_tables(): void
    {
        $this->seed(DatabaseSeeder::class);

        // Verify Users
        $this->assertDatabaseHas('users', ['email' => 'admin@aihealth.com', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'lina@aihealth.com', 'role' => 'doctor']);
        $this->assertDatabaseHas('users', ['email' => 'anaya@aihealth.com', 'role' => 'doctor']);
        $this->assertDatabaseHas('users', ['email' => 'saran@aihealth.com', 'role' => 'doctor']);
        $this->assertDatabaseHas('users', ['email' => 'user@aihealth.com', 'role' => 'user']);

        // Verify Polis
        $this->assertDatabaseCount('polis', 3);
        $this->assertDatabaseHas('polis', ['slug' => 'Cardiologist']);
        $this->assertDatabaseHas('polis', ['slug' => 'Neurologist']);
        $this->assertDatabaseHas('polis', ['slug' => 'Orthopedist']);

        // Verify Doctors & Relations
        $this->assertDatabaseCount('doctors', 3);
        $doc = Doctor::where('spesialisasi', 'Neurologist')->first();
        $this->assertNotNull($doc);
        $this->assertEquals('Neurologi', $doc->poli->nama_poli);
        $this->assertTrue($doc->schedules()->count() > 0);

        // Verify Patients
        $this->assertDatabaseHas('patients', ['mrn' => '#GM-0214', 'full_name' => 'Rina Anggraini']);
        $this->assertDatabaseHas('patients', ['mrn' => '#GM-0188', 'full_name' => 'Budi Santoso']);
        $this->assertDatabaseHas('patients', ['mrn' => '#GM-0205', 'full_name' => 'Siti Rahmawati']);

        // Verify Bookings, Queues & Medical Records
        // Each of the 3 doctors has exactly 1 completed medical record history
        $this->assertDatabaseCount('medical_records', 3);

        $bookingOrtho = Booking::with(['patient', 'doctor', 'queue', 'medicalRecord'])
            ->where('booking_code', '#BILL00124')
            ->first();

        $this->assertNotNull($bookingOrtho);
        $this->assertEquals('Rina Anggraini', $bookingOrtho->patient->full_name);
        $this->assertEquals('Dr. Saran Mitchell', $bookingOrtho->doctor->nama_dokter);
        $this->assertEquals('C-01', $bookingOrtho->queue->queue_number);
        $this->assertNotNull($bookingOrtho->medicalRecord);
        $this->assertStringContainsString('Pain in right knee', $bookingOrtho->medicalRecord->assessmen['primary_diagnosis']);
    }

    /**
     * Test that a patient can create a new consultation booking via API.
     */
    public function test_patient_can_store_booking_via_api(): void
    {
        $this->seed(DatabaseSeeder::class);

        $doctor = Doctor::first();

        $response = $this->postJson(route('booking.store'), [
            'patient_name' => 'Sinta Maharani',
            'phone' => '0812-3456-7890',
            'symptoms' => 'Demam dan flu sejak kemarin malam.',
            'duration' => '< 24 Jam',
            'severity' => 'Ringan',
            'notes' => 'Pasien batuk berdahak.',
            'doctor_id' => $doctor->id,
            'booking_time' => '10:00 AM',
            'booking_date' => now()->toDateString(),
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('patients', [
            'full_name' => 'Sinta Maharani',
            'phone' => '0812-3456-7890',
        ]);

        $this->assertDatabaseHas('bookings', [
            'doctor_id' => $doctor->id,
            'duration' => '< 24 Jam',
            'status' => 'confirmed',
        ]);

        $this->assertDatabaseHas('queues', [
            'time_slot' => '10:00 AM',
            'status' => 'waiting',
        ]);
    }

    /**
     * Test that the manual "Tahap Sesi" switcher buttons are completely removed from Home view.
     */
    public function test_manual_session_stage_switcher_is_removed_from_home_page(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('Tahap Sesi:');
        $response->assertDontSee('btn-toggle-status-before');
        $response->assertDontSee('btn-toggle-status-after');
    }

    /**
     * Test the automatic examination status endpoint and automatic trigger logic.
     */
    public function test_automatic_examination_status_and_doctor_examination_trigger(): void
    {
        $response = $this->getJson(route('appointment.status'));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'mode',
            'doctor_examined',
            'status',
            'booking_code',
            'doctor_name',
            'specialty',
            'total_amount',
            'payment_status',
        ]);
    }

    /**
     * Test that creating a new booking sets mode to 'before', and doctor examination automatically flips it to 'after'.
     */
    public function test_appointment_mode_automatically_flips_when_doctor_examines(): void
    {
        $poli = Poli::create(['slug' => 'Umum', 'nama_poli' => 'Poli Umum', 'name_en' => 'General']);
        $doctor = Doctor::create([
            'poli_id' => $poli->id,
            'nama_dokter' => 'Dr. Dimas Anggara',
            'spesialisasi' => 'Umum',
            'rating' => 4.9,
            'price' => '$20',
            'price_num' => 20,
            'image' => 'images/doctors/doctor-1.png',
        ]);

        // 1. Patient books a new appointment
        $bookingResponse = $this->postJson(route('booking.store'), [
            'patient_name' => 'Rina Anggraini',
            'phone' => '0812-9988-1122',
            'symptoms' => 'Demam dan pusing 2 hari',
            'doctor_id' => $doctor->id,
        ]);
        $bookingResponse->assertOk();

        // 2. Status should be 'before' (doctor has NOT examined yet)
        $statusBefore = $this->getJson(route('appointment.status'));
        $statusBefore->assertOk();
        $statusBefore->assertJson([
            'success' => true,
            'mode' => 'before',
            'doctor_examined' => false,
            'status' => 'confirmed',
        ]);

        // 3. Doctor examines the patient
        $doctorUser = User::factory()->create(['role' => 'doctor']);
        $soapResponse = $this->actingAs($doctorUser)->post(route('doctor.saveSoap', 'Q-001'), [
            'subjective' => 'Pasien mengeluh demam tinggi dan pusing.',
            'primary_diagnosis' => 'A90 - Dengue fever',
            'therapy_plan' => 'Paracetamol 500mg 3x1, Rehidrasi oral',
        ]);
        $soapResponse->assertRedirect();

        // 4. Status should now automatically be 'after' (doctor HAS examined!)
        $statusAfter = $this->getJson(route('appointment.status'));
        $statusAfter->assertOk();
        $statusAfter->assertJson([
            'success' => true,
            'mode' => 'after',
            'doctor_examined' => true,
        ]);

        // 5. Patient pays the bill -> payment saved to database & card transitions to 'welcome'
        $payResponse = $this->postJson(route('payment.process'), [
            'invoice_number' => '#BILL00124',
            'amount' => 435.00,
            'tariff_plan' => 'with_medicine',
            'payment_method' => 'cash',
        ]);
        $payResponse->assertOk();
        $payResponse->assertJson([
            'success' => true,
            'mode' => 'welcome',
        ]);

        $this->assertDatabaseHas('payments', [
            'invoice_number' => '#BILL00124',
            'amount' => 435.00,
            'status' => 'confirmed',
        ]);

        // 6. Verify status endpoint reports 'welcome'
        $statusWelcome = $this->getJson(route('appointment.status'));
        $statusWelcome->assertOk();
        $statusWelcome->assertJson([
            'mode' => 'welcome',
        ]);
    }

    /**
     * Test admin dashboard with payment history and CSV export matching Image 4.
     */
    public function test_admin_dashboard_riwayat_pembayaran_and_csv_export(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('role', 'admin')->first();

        // 1. Admin accesses dashboard
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Hayat Clinic MMC');
        $response->assertSee('Total Visitors');
        $response->assertSee('Surgeries');
        $response->assertSee('Revenue');
        $response->assertSee('Daily Statistics');
        $response->assertSee('January, 2026');
        $response->assertSee("Today's Appointments", false);
        $response->assertSee('Riwayat Pembayaran');
        $response->assertSee('Isa Isgenderov');
        $response->assertSee('Murad Mamedli');
        $response->assertSee('Diana Huseynova');
        $response->assertSee('Export CSV');

        // Verify KPIs are connected to database values
        $response->assertSee((string) Patient::count());
        $response->assertSee((string) Booking::where('status', 'completed')->count());
        $response->assertSee(number_format((float) Payment::where('status', '!=', 'canceled')->sum('net_amount'), 0));

        // 2. Export CSV
        $csvResponse = $this->actingAs($admin)->get(route('admin.exportCsv'));
        $csvResponse->assertOk();
        $this->assertEquals('text/csv; charset=UTF-8', $csvResponse->headers->get('content-type'));
    }

    /**
     * Test admin can create a new transaction/appointment directly from admin dashboard,
     * which creates records in patients, bookings, queues, and payments tables.
     */
    public function test_admin_can_store_transaction_integrated_with_database(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('role', 'admin')->first();
        $doctor = Doctor::first();

        $initialRevenue = (float) Payment::where('status', '!=', 'canceled')->sum('net_amount');

        $response = $this->actingAs($admin)->post(route('admin.storeTransaction'), [
            'patient_name' => 'Kurnia Mega',
            'phone' => '0813-9999-8888',
            'doctor_id' => $doctor->id,
            'booking_date' => now()->toDateString(),
            'booking_time' => '10:00 AM',
            'tariff_plan' => 'with_medicine',
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'symptoms' => 'Pemeriksaan tensi darah dan keluhan pusing',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success');

        // Verify database integration across all models
        $this->assertDatabaseHas('patients', [
            'full_name' => 'Kurnia Mega',
            'phone' => '0813-9999-8888',
        ]);

        $this->assertDatabaseHas('bookings', [
            'doctor_id' => $doctor->id,
            'tariff_plan' => 'with_medicine',
            'total_amount' => 435000.00,
            'payment_status' => 'paid',
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('payments', [
            'patient_name' => 'Kurnia Mega',
            'amount' => 435000.00,
            'payment_method' => 'cash',
            'status' => 'confirmed',
        ]);

        // Verify KPI revenue increased in dashboard
        $newRevenue = (float) Payment::where('status', '!=', 'canceled')->sum('net_amount');
        $this->assertEquals($initialRevenue + 435000.00, $newRevenue);

        $dashboardResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Kurnia Mega');
    }

    /**
     * Test complete 5-step lifecycle requested by user:
     * 1. Pasien request (Booking)
     * 2. Dokter periksa (Doctor sees queue in /doctor)
     * 3. Dokter sudah memeriksa (SOAP saved, status completed)
     * 4. Pasien mendapat bill tagihan (mode 'after', UNPAID tagihan)
     * 5. Pasien selesai membayar (Payment saved in payments table, mode 'welcome', appears in admin)
     */
    public function test_complete_5_step_patient_doctor_payment_lifecycle(): void
    {
        $this->seed(DatabaseSeeder::class);

        $patientUser = User::where('email', 'user@aihealth.com')->first();
        $doctor = Doctor::first();
        $doctorUser = $doctor->user;
        $adminUser = User::where('role', 'admin')->first();

        // Initial state before request: mode should be 'welcome'
        $initialStatus = $this->actingAs($patientUser)->getJson(route('appointment.status'));
        $initialStatus->assertOk();
        $initialStatus->assertJson(['mode' => 'welcome']);

        // STEP 1: Pasien request (Booking Konsultasi)
        $bookingResponse = $this->actingAs($patientUser)->postJson(route('booking.store'), [
            'patient_name' => 'Rina Anggraini',
            'phone' => '0812-9988-7766',
            'symptoms' => 'Migrain berdenyut dan mata berkunang-kunang',
            'doctor_id' => $doctor->id,
            'booking_time' => '10:00 AM',
        ]);
        $bookingResponse->assertOk();
        $bookingData = $bookingResponse->json('booking');
        $this->assertNotNull($bookingData);
        $bookingId = $bookingData['id'];

        // Card mode is now 'before' (NEXT APPOINTMENT - Upcoming)
        $statusStep1 = $this->actingAs($patientUser)->getJson(route('appointment.status'));
        $statusStep1->assertOk();
        $statusStep1->assertJson([
            'mode' => 'before',
            'doctor_examined' => false,
        ]);

        // STEP 2: Dokter periksa (Doctor opens /doctor and sees queue)
        $doctorDashboard = $this->actingAs($doctorUser)->get(route('doctor.dashboard'));
        $doctorDashboard->assertOk();

        $queue = Queue::where('booking_id', $bookingId)->first();
        $this->assertNotNull($queue);
        $queueKey = 'Q-'.str_pad((string) $queue->id, 3, '0', STR_PAD_LEFT);

        // STEP 3: Dokter sudah memeriksa (Doctor fills SOAP & completes examination)
        $soapResponse = $this->actingAs($doctorUser)->post(route('doctor.saveSoap', $queueKey), [
            'subjective' => 'Pasien mengeluh migrain hebat sejak kemarin.',
            'primary_diagnosis' => 'G43.1 - Classic Migraine',
            'therapy_plan' => "Sumatriptan 50mg 1x1\nParacetamol 500mg 3x1",
        ]);
        $soapResponse->assertRedirect();

        $this->assertDatabaseHas('medical_records', [
            'booking_id' => $bookingId,
        ]);

        // STEP 4: Pasien mendapat bill tagihan (mode 'after' - UNPAID bill)
        $statusStep4 = $this->actingAs($patientUser)->getJson(route('appointment.status'));
        $statusStep4->assertOk();
        $statusStep4->assertJson([
            'mode' => 'after',
            'doctor_examined' => true,
            'payment_status' => 'unpaid',
        ]);

        // STEP 5: Pasien selesai membayar (Payment saved to database, mode 'welcome')
        $payResponse = $this->actingAs($patientUser)->postJson(route('payment.process'), [
            'booking_id' => $bookingId,
            'invoice_number' => $bookingData['booking_code'],
            'amount' => 435.00,
            'tariff_plan' => 'with_medicine',
            'payment_method' => 'cash',
        ]);
        $payResponse->assertOk();
        $payResponse->assertJson([
            'success' => true,
            'mode' => 'welcome',
        ]);

        // Verify payment is saved to payments table
        $this->assertDatabaseHas('payments', [
            'booking_id' => $bookingId,
            'amount' => 435.00,
            'status' => 'confirmed',
        ]);

        // Verify patient card is now 'welcome' (Tagihan card removed)
        $statusStep5 = $this->actingAs($patientUser)->getJson(route('appointment.status'));
        $statusStep5->assertOk();
        $statusStep5->assertJson([
            'mode' => 'welcome',
        ]);

        // Verify transaction appears in Admin Riwayat Pembayaran
        $adminDashboard = $this->actingAs($adminUser)->get(route('admin.dashboard'));
        $adminDashboard->assertOk();
        $adminDashboard->assertSee($bookingData['booking_code']);
    }

    /**
     * Test that each doctor has an account, can only view their own patient history (1 history each),
     * cannot view other doctors' patients, and admin can view all patients across all doctors.
     */
    public function test_each_doctor_has_account_isolated_patient_history_and_admin_sees_all(): void
    {
        $this->seed(DatabaseSeeder::class);

        $docUserLina = User::where('email', 'lina@aihealth.com')->first();
        $docUserAnaya = User::where('email', 'anaya@aihealth.com')->first();
        $docUserSaran = User::where('email', 'saran@aihealth.com')->first();
        $adminUser = User::where('email', 'admin@aihealth.com')->first();

        $this->assertNotNull($docUserLina, 'Akun Dr. Lina harus ada');
        $this->assertNotNull($docUserAnaya, 'Akun Dr. Anaya harus ada');
        $this->assertNotNull($docUserSaran, 'Akun Dr. Saran harus ada');
        $this->assertNotNull($adminUser, 'Akun Admin harus ada');

        $docLina = Doctor::where('nama_dokter', 'Dr. Lina Wijaya')->first();
        $docAnaya = Doctor::where('nama_dokter', 'Dr. Anaya Kapoor')->first();
        $docSaran = Doctor::where('nama_dokter', 'Dr. Saran Mitchell')->first();

        // 1. Verify each doctor currently has exactly 1 completed consultation/medical record in database
        $this->assertEquals(1, $docLina->bookings()->where('status', 'completed')->count(), 'Dr. Lina harus memiliki tepat 1 riwayat selesai');
        $this->assertEquals(1, $docAnaya->bookings()->where('status', 'completed')->count(), 'Dr. Anaya harus memiliki tepat 1 riwayat selesai');
        $this->assertEquals(1, $docSaran->bookings()->where('status', 'completed')->count(), 'Dr. Saran harus memiliki tepat 1 riwayat selesai');

        // 2. Doctor Lina Portal View (/doctor):
        // Dr. Lina sees her patient (Siti Rahmawati) and does NOT see Dr. Anaya or Dr. Saran's patients
        $responseLina = $this->actingAs($docUserLina)->get(route('doctor.dashboard'));
        $responseLina->assertOk();
        $responseLina->assertSee('Dr. Lina Wijaya');
        $responseLina->assertSee('Siti Rahmawati');
        $responseLina->assertDontSee('Rina Anggraini');

        // 3. Doctor Anaya Portal View (/doctor):
        // Dr. Anaya sees her patient (Budi Santoso) and does NOT see Dr. Lina or Dr. Saran's completed patients
        $responseAnaya = $this->actingAs($docUserAnaya)->get(route('doctor.dashboard'));
        $responseAnaya->assertOk();
        $responseAnaya->assertSee('Dr. Anaya Kapoor');
        $responseAnaya->assertSee('Budi Santoso');
        $responseAnaya->assertDontSee('Siti Rahmawati');

        // 4. Doctor Saran Portal View (/doctor):
        // Dr. Saran sees his patient (Rina Anggraini) and does NOT see Dr. Lina or Dr. Anaya's patients
        $responseSaran = $this->actingAs($docUserSaran)->get(route('doctor.dashboard'));
        $responseSaran->assertOk();
        $responseSaran->assertSee('Dr. Saran Mitchell');
        $responseSaran->assertSee('Rina Anggraini');
        $responseSaran->assertDontSee('Siti Rahmawati');

        // 5. Admin Portal View (/admin):
        // Admin can see ALL patients across all doctors!
        $responseAdmin = $this->actingAs($adminUser)->get(route('admin.dashboard'));
        $responseAdmin->assertOk();
        $responseAdmin->assertSee('Siti Rahmawati'); // Dr. Lina's patient
        $responseAdmin->assertSee('Budi Santoso');   // Dr. Anaya's patient
        $responseAdmin->assertSee('Rina Anggraini'); // Dr. Saran's patient
        $responseAdmin->assertSee('Dr. Lina Wijaya');
        $responseAdmin->assertSee('Dr. Anaya Kapoor');
        $responseAdmin->assertSee('Dr. Saran Mitchell');
    }
}
