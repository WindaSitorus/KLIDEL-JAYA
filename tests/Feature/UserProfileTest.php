<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * 1. Test guest cannot view profile page.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('profile.edit'));
        $response->assertRedirect(route('login'));
    }

    /**
     * 2. Test authenticated users across roles (Admin, Dokter, Apoteker, Pasien) can view profile.
     */
    public function test_users_of_all_roles_can_view_profile_page(): void
    {
        $admin = User::where('role', 'admin')->first();
        $doctor = User::where('role', 'doctor')->first();
        $apoteker = User::where('role', 'apoteker')->first();
        $patient = User::where('role', 'user')->first();

        $this->actingAs($admin)->get(route('profile.edit'))
            ->assertOk()
            ->assertSee($admin->name)
            ->assertSee('Administrator');

        $this->actingAs($doctor)->get(route('profile.edit'))
            ->assertOk()
            ->assertSee($doctor->name)
            ->assertSee('Dokter Spesialis');

        $this->actingAs($apoteker)->get(route('profile.edit'))
            ->assertOk()
            ->assertSee($apoteker->name)
            ->assertSee('Apoteker');

        $this->actingAs($patient)->get(route('profile.edit'))
            ->assertOk()
            ->assertSee($patient->name)
            ->assertSee('Pasien');
    }

    /**
     * 3. Test user can update basic profile info (name, username, email, phone).
     */
    public function test_user_can_update_profile_info(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Rina Anggraini Updated',
            'username' => 'rina_ang99',
            'email' => 'rina.updated@example.com',
            'phone' => '+62 812-9988-7766',
        ]);

        $response->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Rina Anggraini Updated',
            'username' => 'rina_ang99',
            'email' => 'rina.updated@example.com',
            'phone' => '+62 812-9988-7766',
        ]);
    }

    /**
     * 4. Test user can upload and replace profile avatar via Laravel Storage.
     */
    public function test_user_can_upload_and_replace_avatar(): void
    {
        Storage::fake('public');

        $user = User::where('role', 'user')->first();
        $avatarFile = UploadedFile::fake()->image('profile_pic.jpg', 200, 200);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $avatarFile,
        ]);

        $response->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success');

        $freshUser = $user->fresh();
        $this->assertNotNull($freshUser->avatar);
        Storage::disk('public')->assertExists($freshUser->avatar);

        // Test delete / remove avatar
        $deleteResponse = $this->actingAs($user)->delete(route('profile.avatar.remove'));
        $deleteResponse->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success');

        $this->assertNull($user->fresh()->avatar);
    }

    /**
     * 5. Test user cannot change their role or ID through the profile update form.
     */
    public function test_user_cannot_alter_role_or_id(): void
    {
        $patient = User::where('role', 'user')->first();
        $originalId = $patient->id;

        // Attempt privilege escalation or ID injection
        $response = $this->actingAs($patient)->put(route('profile.update'), [
            'name' => 'Attempt Escalation',
            'email' => $patient->email,
            'role' => 'admin',
            'id' => 9999,
        ]);

        $response->assertRedirect(route('profile.edit'));

        $refreshed = $patient->fresh();
        $this->assertEquals('user', $refreshed->role, 'Role akun tidak boleh berubah melalui profil!');
        $this->assertEquals($originalId, $refreshed->id, 'ID akun tidak boleh berubah!');
    }

    /**
     * 6. Test email uniqueness validation: ignores current user but rejects duplicate from others.
     */
    public function test_email_uniqueness_validation(): void
    {
        $user1 = User::where('role', 'user')->first();
        $user2 = User::where('role', 'admin')->first();

        // 1. Same email for current user should PASS
        $resSame = $this->actingAs($user1)->put(route('profile.update'), [
            'name' => $user1->name,
            'email' => $user1->email,
        ]);
        $resSame->assertSessionHasNoErrors();

        // 2. Email belonging to user2 should FAIL
        $resDuplicate = $this->actingAs($user1)->put(route('profile.update'), [
            'name' => $user1->name,
            'email' => $user2->email,
        ]);
        $resDuplicate->assertSessionHasErrors(['email']);
    }

    /**
     * 7. Test user can change password with correct current password.
     */
    public function test_user_can_change_password(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'NewStrongPass123!',
            'password_confirmation' => 'NewStrongPass123!',
        ]);

        $response->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('NewStrongPass123!', $user->fresh()->password));
    }

    /**
     * 8. Test password change fails when current password is wrong.
     */
    public function test_password_change_fails_with_wrong_current_password(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'wrong_password_here',
            'password' => 'NewStrongPass123!',
            'password_confirmation' => 'NewStrongPass123!',
        ]);

        $response->assertSessionHasErrors(['current_password']);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
