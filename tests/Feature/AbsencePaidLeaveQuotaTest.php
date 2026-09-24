<?php

namespace Tests\Feature;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsencePaidLeaveQuotaTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_use_up_to_25_paid_leave_days_in_a_year(): void
    {
        $user = User::factory()->create();

        $this->post('/absences', [
            'user_id' => $user->id,
            'date_debut' => '2026-01-01',
            'date_fin' => '2026-01-25',
            'motif' => 'Congé payé',
        ])->assertRedirect('/absences');

        $this->assertDatabaseHas('absences', [
            'user_id' => $user->id,
            'motif' => 'Congé payé',
        ]);
    }

    public function test_employee_cannot_exceed_25_paid_leave_days_in_a_year(): void
    {
        $user = User::factory()->create();

        Absence::create([
            'user_id' => $user->id,
            'date_debut' => '2026-01-01',
            'date_fin' => '2026-01-20',
            'motif' => 'Congé payé',
        ]);

        $this->post('/absences', [
            'user_id' => $user->id,
            'date_debut' => '2026-02-01',
            'date_fin' => '2026-02-06',
            'motif' => 'Congé payé',
        ])->assertSessionHasErrors('date_fin');
    }
}
