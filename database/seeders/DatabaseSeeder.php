<?php

namespace Database\Seeders;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory()->count(2)->create();

        Absence::factory()
            ->count(2)
            ->sequence(
                ['user_id' => $users[0]->id],
                ['user_id' => $users[1]->id],
            )
            ->create();
    }
}
