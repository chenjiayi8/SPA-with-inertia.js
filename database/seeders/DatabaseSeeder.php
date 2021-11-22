<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\Models\User::factory()->create([
            'name' => "Jiayi Chen"
        ]);
        \App\Models\Management::factory()->create([
            'user_id' => $user->id,
            'workspace' => 'NLearn'
        ]);
        \App\Models\Management::factory()->appendGroups(3);

        \App\Models\User::factory(50)->create();
        \App\Models\Management::factory(3)->create();

    }



}
