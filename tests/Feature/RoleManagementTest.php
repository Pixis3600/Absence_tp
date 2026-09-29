<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Bouncer::allow('admin')->to('user-view-all');
        Role::query()->firstOrCreate(['name' => 'admin']);
        Role::query()->firstOrCreate(['name' => 'salarie']);
    }

    public function test_only_admin_can_access_roles_page(): void
    {
        $admin = User::factory()->create();
        Bouncer::assign('admin')->to($admin);

        $employee = User::factory()->create();
        Bouncer::assign('salarie')->to($employee);

        $this->actingAs($admin)
            ->get(route('roles.index'))
            ->assertOk();

        $this->actingAs($employee)
            ->get(route('roles.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_a_role_with_abilities(): void
    {
        $admin = User::factory()->create();
        Bouncer::assign('admin')->to($admin);

        $this->actingAs($admin)
            ->post(route('roles.store'), [
                'name' => 'manager',
                'new_abilities' => 'report-export,absence-review',
            ])
            ->assertRedirect(route('roles.index'));

        $role = Role::query()->where('name', 'manager')->firstOrFail();
        $this->assertCount(2, $role->abilities);

        $this->actingAs($admin)
            ->put(route('roles.update', $role), [
                'name' => 'manager_plus',
                'abilities' => [$role->abilities->first()->id],
                'new_abilities' => 'user-create',
            ])
            ->assertRedirect(route('roles.index'));

        $role->refresh();
        $role->load('abilities');

        $this->assertSame('manager_plus', $role->name);
        $this->assertTrue($role->abilities->pluck('name')->contains('user-create'));

        $this->actingAs($admin)
            ->delete(route('roles.destroy', $role))
            ->assertRedirect(route('roles.index'));

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }
}
