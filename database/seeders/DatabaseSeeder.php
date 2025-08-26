<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $this->call([
            CarBrandSeeder::class,
            CarModelSeeder::class,
            CarSeeder::class,
        ]);

        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $users = User::factory(5)->create();

        $cars = Car::all();

        $testUser->cars()->attach($cars->take(3)->pluck('id'));

        foreach ($users as $user) {
            $user->cars()->attach(
                $cars->random(rand(1, 2))->pluck('id')
            );
        }
    }
}
