<?php

use Illuminate\Database\Seeder;
use App\Workout;

class WorkoutTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();

        foreach (range(1, 30) as $index) {
            Workout::create([
                'workout_description' => $faker->sentence(),
                'workout_type' => true,
                'count_for_work' => true,
                'prescribed' => false,
                'results' => $faker->sentence(),
                'user_id' => $faker->numberBetween($min = 1, $max = 5)
            ]);
        }
    }
}
