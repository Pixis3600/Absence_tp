<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Role;
use Tests\TestCase;

class UserRoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::query()->firstOrCreate(['name' => 'admin']);
        Role::query()->firstOrCreate(['name' => 'salarie']);

        Bouncer::allow('admin')->to('user-view-all');
        Bouncer::allow('admin')->to('user-create');
        Bouncer::allow('admin')->to('user-delete');
    }

    public function test_an_admin_can_assign_a_role_when_creating_a_user(): void
    {
        $admin = User::factory()->create();
        Bouncer::assign('admin')->to($admin);

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Alice',
                'lastname' => 'Martin',
                'email' => 'alice@example.com',
                'password' => 'secret123',
                'role' => 'salarie',
            ])
            ->assertRedirect(route('users.index'));

        $createdUser = User::where('email', 'alice@example.com')->firstOrFail();

        $this->assertTrue($createdUser->isA('salarie'));
    }

    public function test_an_admin_can_change_a_user_role(): void
    {
        $admin = User::factory()->create();
        Bouncer::assign('admin')->to($admin);

        $user = User::factory()->create();
        Bouncer::assign('salarie')->to($user);

        $this->actingAs($admin)
            ->put(route('users.update', $user), [
                'name' => $user->name,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'password' => '',
                'role' => 'admin',
            ])
            ->assertRedirect(route('users.index'));

        $user->refresh();

        $this->assertTrue($user->isA('admin'));
    }

    public function test_a_non_admin_cannot_change_a_user_role(): void
    {
        $employee = User::factory()->create();
        $user = User::factory()->create();
        Bouncer::assign('salarie')->to($user);

        $this->actingAs($employee)
            ->put(route('users.update', $user), [
                'name' => $user->name,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'password' => '',
                'role' => 'admin',
            ])
            ->assertForbidden();
    }
}
