<?php

namespace Tests\Feature;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsenceStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_absence_can_be_accepted_or_rejected(): void
    {
        $user = User::factory()->create();
        $absence = Absence::factory()->create([
            'user_id' => $user->id,
            'status' => 'en_attente',
        ]);

        $this->actingAs($user)
            ->post(route('absences.accept', $absence))
            ->assertRedirect();

        $absence->refresh();
        $this->assertSame('accepte', $absence->status);

        $this->actingAs($user)
            ->post(route('absences.reject', $absence))
            ->assertRedirect();

        $absence->refresh();
        $this->assertSame('refuse', $absence->status);
    }
}
