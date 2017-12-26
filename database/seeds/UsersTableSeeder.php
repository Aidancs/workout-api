<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();

        foreach (range(1, 5) as $index) {
            User::create([
                'name' => $faker->userName,
                'email' => $faker->email,
                'age' => '39',
                'height' => '5\'7"',
                'weight' => '223',
                'date_started' => $faker->date,
                'password' => bcrypt('secret')
            ]);
        }
    }
}
