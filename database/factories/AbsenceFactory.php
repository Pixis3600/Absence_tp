<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Absence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Absence>
 */
class AbsenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateDebut = fake()->dateTimeBetween('-6 months', 'now');
        $dateFin = (clone $dateDebut)->modify('+' . fake()->numberBetween(0, 10) . ' days');

        return [
            'user_id' => User::factory(),
            'date_debut' => $dateDebut->format('Y-m-d'),
            'date_fin' => $dateFin->format('Y-m-d'),
            'motif' => fake()->sentence(6),
            'status' => fake()->randomElement(['en_attente', 'accepte', 'refuse']),
        ];
    }
}
