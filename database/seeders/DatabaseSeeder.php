<?php

namespace Database\Seeders;

use App\Models\Gifs;
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
        $this->seedGifTable();
    }

    private function seedUserTable(): void
    {
        $faker = Faker::create();

        foreach(range(1, 5) as $idx) {
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

    private function seedGifTable(): void
    {
        $faker = Faker::create('es_ES');

        $gifs = [];

        foreach(range(1, 50) as $idx){

            $toInsertGif = [];

            $toInsertGif['external_id'] = $faker->uuid;
            $toInsertGif['title'] = $faker->realText;
            $toInsertGif['url'] = $faker->url();

            $gifs[] = $toInsertGif;

        }

        Gifs::insert($gifs);
    }
}
