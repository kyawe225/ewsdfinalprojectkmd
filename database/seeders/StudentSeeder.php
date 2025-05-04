<?php
namespace Database\Seeders;

use App\Models\Student;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure a clean slate on each run
        Student::truncate();

        $faker = Faker::create();

        for ($i = 1; $i <= 100; $i++) {
            // One fixed default email; others random
            $email = $i === 1
            ? 'eschmitt@example.com'
            : $faker->unique()->safeEmail;

            Student::create([
                'StudentID'    => sprintf('STD%03d', $i),
                'name'         => $faker->name,
                'email'        => $email,
                'password'     => bcrypt('password'),
                'phone_number' => $faker->numerify('097900#####'),
                'created_at'   => Carbon::now('UTC'),
                'updated_at'   => Carbon::now('UTC'),
            ]);

            // Assign role if Spatie’s role package is in use
            if (method_exists($this, 'assignRole')) {
                $student->assignRole('student');
            }
        }
    }
}
