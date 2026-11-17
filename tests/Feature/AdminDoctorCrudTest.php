<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDoctorCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * Test admin can access the doctor management page.
     */
    public function test_admin_can_view_doctors_management_page(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.doctors.index'));

        $response->assertOk();
        $response->assertViewIs('admin.doctors.index');
        $response->assertSee('Manajemen Dokter');
        $response->assertSee('Dr. Lina Wijaya');
        $response->assertSee('Dr. Anaya Kapoor');
        $response->assertSee('Dr. Saran Mitchell');
    }

    /**
     * Test admin can register a new doctor along with their login user account.
     */
    public function test_admin_can_register_new_doctor_with_login_account(): void
    {
        $admin = User::where('role', 'admin')->first();
        $poli = Poli::first();

        $newDoctorData = [
            'nama_dokter' => 'Dr. Budi Santoso, Sp.A',
            'poli_id' => $poli->id,
            'spesialisasi' => 'Spesialis Anak (Pediatrician)',
            'price_num' => 195000,
            'availability' => '08:00 - 16:00 WIB',
            'experience' => '8 tahun',
            'patients' => '2.5k Up',
            'rating' => 4.9,
            'image_preset' => 'images/doctors/doctor-1.png',
            'email' => 'budisantoso@aihealth.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->actingAs($admin)->post(route('admin.doctors.store'), $newDoctorData);

        $response->assertRedirect(route('admin.doctors.index'));
        $response->assertSessionHas('success');

        // 1. Verify User account was created in `users` table
        $this->assertDatabaseHas('users', [
            'name' => 'Dr. Budi Santoso, Sp.A',
            'email' => 'budisantoso@aihealth.com',
            'role' => 'doctor',
        ]);

        $createdUser = User::where('email', 'budisantoso@aihealth.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue(Hash::check('secret123', $createdUser->password));

        // 2. Verify Doctor profile was created in `doctors` table linked to user
        $this->assertDatabaseHas('doctors', [
            'nama_dokter' => 'Dr. Budi Santoso, Sp.A',
            'user_id' => $createdUser->id,
            'poli_id' => $poli->id,
            'spesialisasi' => 'Spesialis Anak (Pediatrician)',
            'price_num' => 195000.00,
            'price' => 'Rp 195.000',
        ]);

        // 3. Verify Schedule was automatically generated
        $createdDoctor = Doctor::where('user_id', $createdUser->id)->first();
        $this->assertNotNull($createdDoctor);
        $this->assertDatabaseHas('schedules', [
            'doctor_id' => $createdDoctor->id,
        ]);
    }

    /**
     * Test that the newly registered doctor can immediately log into the doctor portal.
     */
    public function test_newly_registered_doctor_can_login_to_doctor_portal(): void
    {
        $admin = User::where('role', 'admin')->first();
        $poli = Poli::first();

        // Admin registers doctor
        $this->actingAs($admin)->post(route('admin.doctors.store'), [
            'nama_dokter' => 'Dr. Jessica Tan, Sp.KK',
            'poli_id' => $poli->id,
            'spesialisasi' => 'Dermatologis & Venereologi',
            'price_num' => 210000,
            'email' => 'jessica@aihealth.com',
            'password' => 'passwordDokter123',
            'password_confirmation' => 'passwordDokter123',
        ]);

        // Logout admin
        $this->post(route('logout'));

        // New doctor logs in
        $loginResponse = $this->post(route('login.post'), [
            'email' => 'jessica@aihealth.com',
            'password' => 'passwordDokter123',
        ]);

        $loginResponse->assertRedirect(route('doctor.dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isDoctor());
    }

    /**
     * Test that the newly registered doctor immediately appears on the patient home page.
     */
    public function test_newly_registered_doctor_appears_on_patient_home_page(): void
    {
        $admin = User::where('role', 'admin')->first();
        $poli = Poli::first();

        // Admin registers new doctor
        $this->actingAs($admin)->post(route('admin.doctors.store'), [
            'nama_dokter' => 'Dr. Hendrik Pratama, Sp.THT',
            'poli_id' => $poli->id,
            'spesialisasi' => 'Spesialis Telinga Hidung Tenggorokan',
            'price_num' => 165000,
            'email' => 'hendrik@aihealth.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Patient visits home page
        $homeResponse = $this->get(route('home'));

        $homeResponse->assertOk();
        $homeResponse->assertSee('Dr. Hendrik Pratama, Sp.THT');
        $homeResponse->assertSee('Spesialis Telinga Hidung Tenggorokan');
        $homeResponse->assertSee('Rp 165.000');
    }

    /**
     * Test admin can update doctor data and their login account.
     */
    public function test_admin_can_update_doctor_and_login_account(): void
    {
        $admin = User::where('role', 'admin')->first();
        $doctor = Doctor::with('user')->first();
        $newPoli = Poli::latest('id')->first();

        $updateData = [
            'nama_dokter' => 'Dr. Lina Wijaya Senior, Sp.JP',
            'poli_id' => $newPoli->id,
            'spesialisasi' => 'Konsultan Kardiologi Intervensi',
            'price_num' => 250000,
            'availability' => '07:00 - 15:00 WIB',
            'experience' => '15 tahun',
            'patients' => '5.0k Up',
            'email' => 'lina.senior@aihealth.com',
            'password' => 'newPassword456',
            'password_confirmation' => 'newPassword456',
        ];

        $response = $this->actingAs($admin)->put(route('admin.doctors.update', $doctor->id), $updateData);

        $response->assertRedirect(route('admin.doctors.index'));

        // Verify updated Doctor profile
        $this->assertDatabaseHas('doctors', [
            'id' => $doctor->id,
            'nama_dokter' => 'Dr. Lina Wijaya Senior, Sp.JP',
            'poli_id' => $newPoli->id,
            'spesialisasi' => 'Konsultan Kardiologi Intervensi',
            'price_num' => 250000.00,
            'price' => 'Rp 250.000',
        ]);

        // Verify updated User account & password
        $this->assertDatabaseHas('users', [
            'id' => $doctor->user_id,
            'email' => 'lina.senior@aihealth.com',
        ]);

        $doctor->user->refresh();
        $this->assertTrue(Hash::check('newPassword456', $doctor->user->password));
    }

    /**
     * Test admin can delete doctor and their associated login account safely.
     */
    public function test_admin_can_delete_doctor_and_login_account(): void
    {
        $admin = User::where('role', 'admin')->first();
        $doctor = Doctor::with('user')->first();
        $userId = $doctor->user_id;
        $doctorId = $doctor->id;

        $response = $this->actingAs($admin)->delete(route('admin.doctors.destroy', $doctorId));

        $response->assertRedirect(route('admin.doctors.index'));

        // Verify Doctor profile deleted
        $this->assertDatabaseMissing('doctors', ['id' => $doctorId]);

        // Verify linked doctor User account deleted
        if ($userId) {
            $this->assertDatabaseMissing('users', ['id' => $userId]);
        }
    }

    /**
     * Test non-admin cannot access doctor management.
     */
    public function test_non_admin_cannot_access_doctor_crud(): void
    {
        $user = User::where('role', 'user')->first();

        // 1. Regular user trying to access index
        $indexResponse = $this->actingAs($user)->get(route('admin.doctors.index'));
        $indexResponse->assertRedirect(route('home'));

        // 2. Regular user trying to create doctor
        $storeResponse = $this->actingAs($user)->post(route('admin.doctors.store'), [
            'nama_dokter' => 'Hacker Doctor',
            'poli_id' => 1,
            'price_num' => 100000,
            'email' => 'hacker@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $storeResponse->assertRedirect(route('home'));
    }
}
