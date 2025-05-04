<?php
namespace Database\Seeders;

use App\Models\Tutor;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class TutorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table before seeding to avoid duplicates
        Tutor::truncate();

        $faker = Faker::create();

        // Define a list of possible specializations
        $specializations = [
            'Mathematics',
            'English',
            'Science',
            'History',
            'Computer Science',
        ];

        // Change the count as needed
        for ($i = 1; $i <= 40; $i++) {
            $tutorData = [
                "name" => $faker->name,
                "email" => $faker->unique()->safeEmail,
                "password" => bcrypt("password"), // Always hashed using bcrypt
                "phone_number" => $faker->numerify('097900#####'),
                "specialization" => $faker->randomElement($specializations),
                "created_at" => Carbon::now('UTC'),
                "updated_at" => Carbon::now('UTC'),
            ];

            if($i == 1) $tutorData['email'] = "myothandar234@outlook.com";

            $tutor = Tutor::create($tutorData);
            // Assign the "tutor" role
            $tutor->assignRole('tutor');
        }

        $tutorData = [
            "id"=>41,
            "name" => $faker->name,
            "email" => "myothandar234@outlook.com",
            "password" => bcrypt("password"), // Always hashed using bcrypt
            "phone_number" => $faker->numerify('097900#####'),
            "specialization" => $faker->randomElement($specializations),
            "created_at" => Carbon::now('UTC'),
            "updated_at" => Carbon::now('UTC'),
        ];
        $tutor = Tutor::create($tutorData);
        // Assign the "tutor" role
        $tutor->assignRole('tutor');
    }
}
