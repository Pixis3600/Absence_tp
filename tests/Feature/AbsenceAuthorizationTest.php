<?php

namespace Tests\Feature;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsenceAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_sees_only_his_own_absences_and_cannot_edit_another_users_absence(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['is_admin' => false]);

        $ownAbsence = Absence::factory()->create([
            'user_id' => $user->id,
            'motif' => 'Congé payé',
            'status' => 'en_attente',
        ]);

        $otherAbsence = Absence::factory()->create([
            'user_id' => $admin->id,
            'motif' => 'Congé maladie',
            'status' => 'en_attente',
        ]);

        $this->actingAs($user)
            ->get(route('absences.index'))
            ->assertOk()
            ->assertSee($ownAbsence->motif)
            ->assertDontSee($otherAbsence->motif);

        $this->actingAs($user)
            ->get(route('absences.edit', $otherAbsence))
            ->assertForbidden();
    }

    public function test_only_admins_can_accept_or_reject_an_absence(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['is_admin' => false]);
        $absence = Absence::factory()->create([
            'user_id' => $user->id,
            'status' => 'en_attente',
        ]);

        $this->actingAs($user)
            ->post(route('absences.accept', $absence))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('absences.accept', $absence))
            ->assertRedirect();
    }

    public function test_a_user_cannot_edit_a_processed_absence(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $absence = Absence::factory()->create([
            'user_id' => $user->id,
            'status' => 'accepte',
        ]);

        $this->actingAs($user)
            ->get(route('absences.edit', $absence))
            ->assertForbidden();
    }
}
