<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, UserSeeder::class]);
    }

    private function as(string $email): self
    {
        return $this->actingAs(User::where('email', $email)->firstOrFail());
    }

    public function test_guest_is_redirected_to_login_for_internal_areas(): void
    {
        foreach (['/admin', '/staff/reservations', '/chef/dashboard'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_customer_cannot_enter_internal_areas(): void
    {
        foreach (['/admin', '/admin/users', '/staff/reservations', '/chef/dashboard'] as $url) {
            $this->as('customer@gmail.com')->get($url)->assertRedirect(url('/'));
        }
    }

    public function test_staff_and_chef_cannot_enter_admin_area(): void
    {
        foreach (['staff@gmail.com', 'chef@gmail.com'] as $email) {
            foreach (['/admin', '/admin/users', '/admin/menus', '/admin/bills'] as $url) {
                $this->as($email)->get($url)->assertRedirect()->assertSessionHas('error');
            }
        }
    }

    public function test_chef_cannot_enter_staff_area_and_staff_cannot_enter_chef_area(): void
    {
        $this->as('chef@gmail.com')->get('/staff/reservations')->assertRedirect(route('chef.index'));
        $this->as('staff@gmail.com')->get('/chef/dashboard')->assertRedirect(route('staff.reservations.index'));
    }

    public function test_cashier_module_is_gone(): void
    {
        $this->as('admin@gmail.com')->get('/cashier')->assertNotFound();
        $this->assertDatabaseMissing('roles', ['name' => 'cashier']);
        $this->assertFalse(\Route::has('cashier.index'));
    }

    public function test_debug_route_removed(): void
    {
        $this->get('/test-models')->assertNotFound();
    }

    public function test_login_redirects_by_role(): void
    {
        $cases = [
            'admin@gmail.com'    => route('admin.index'),
            'staff@gmail.com'    => route('staff.reservations.index'),
            'chef@gmail.com'     => route('chef.index'),
            'customer@gmail.com' => url('/'),
        ];

        foreach ($cases as $email => $target) {
            $this->post('/login', ['email' => $email, 'password' => 'Demo@123456'])
                 ->assertRedirect($target);
            auth()->logout();
        }
    }

    public function test_registration_always_creates_customer_even_if_role_id_is_sent(): void
    {
        $this->post('/register', [
            'name' => 'Hacker', 'email' => 'h@x.com', 'phone' => '0911111111',
            'password' => 'Password123', 'password_confirmation' => 'Password123',
            'role_id' => Role::ADMIN,
        ])->assertOk();

        $this->assertSame(Role::CUSTOMER, (int) User::where('email', 'h@x.com')->value('role_id'));
    }

    public function test_role_id_is_not_mass_assignable(): void
    {
        $user = new User(['name' => 'x', 'role_id' => Role::ADMIN]);
        $this->assertNull($user->role_id);
    }
}
