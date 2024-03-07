<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedUserTable();
    }

    private function seedUserTable(): void
    {
        $faker = Faker::create();

        foreach (range(1, 5) as $idx) {
            DB::table('prex_user')->insert([
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
            ]);
        }

        DB::table('prex_user')->insert([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
    }
}
