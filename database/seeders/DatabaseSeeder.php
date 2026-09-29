<?php

namespace Database\Seeders;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade as Bouncer;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory()->count(2)->create();

        Bouncer::allow('admin')->to('user-view-all');
        Bouncer::allow('admin')->to('user-create');
        Bouncer::allow('admin')->to('user-delete');
        Bouncer::allow('admin')->to('absence-view-all');
        Bouncer::allow('admin')->to('absence-edit-all');
        Bouncer::allow('admin')->to('absence-delete-all');
        Bouncer::allow('admin')->to('absence-review');

        Bouncer::allow('salarie')->to('absence-create');
        Bouncer::allow('salarie')->to('absence-view-own');
        Bouncer::allow('salarie')->to('absence-edit-own');

        Bouncer::assign('admin')->to($users[0]);
        Bouncer::assign('salarie')->to($users[1]);

        Absence::factory()
            ->count(2)
            ->sequence(
                ['user_id' => $users[0]->id],
                ['user_id' => $users[1]->id],
            )
            ->create();
    }
}
