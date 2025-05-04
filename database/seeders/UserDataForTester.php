<?php
namespace Database\Seeders;

use App\Models\Staff;
use App\Models\Student;
use App\Models\Tutor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserDataForTester extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optionally, clear all existing data to avoid duplicate entries.
        Staff::truncate();
        Tutor::truncate();
        Student::truncate();

        /*
         * 1. Seed Admin user (using the Staff model)
         */
        $adminData = [
            'name'         => "Mr.Pyay Thi Oo",
            'email'        => "pyayt@gmail.com",
            'password'     => bcrypt("password"),
            'phone_number' => "0000000000", // Use appropriate phone number if available.
            'created_at'   => Carbon::now('UTC'),
            'updated_at'   => Carbon::now('UTC'),
        ];

        $admin = Staff::create($adminData);
        // Assign the "admin" role to the user; ensure you have defined this role in your permissions.
        $admin->assignRole('staff');

        /*
         * 2. Seed Tutors
         */
        $tutors = [
            [
                'name'           => "Mr.Nay Toe",
                'email'          => "nayt@gmail.com",
                'password'       => bcrypt("password"),
                'phone_number'   => "0000000001",
                'specialization' => "General", // Change as needed.
                'created_at'     => Carbon::now('UTC'),
                'updated_at'     => Carbon::now('UTC'),
            ],
            [
                'name'           => "Mr.Justin",
                'email'          => "justin@gmail.com",
                'password'       => bcrypt("password"),
                'phone_number'   => "0000000002",
                'specialization' => "General", // Change as needed.
                'created_at'     => Carbon::now('UTC'),
                'updated_at'     => Carbon::now('UTC'),
            ],
        ];

        foreach ($tutors as $tutorData) {
            $tutor = Tutor::create($tutorData);
            // Assign the "tutor" role
            $tutor->assignRole('tutor');
        }

        /*
         * 3. Seed Students
         */
        $students = [
            [
                'StudentID'    => 'STU001',
                'name'         => "Mis.Daisy",
                'email'        => "daisy@gmail.com",
                'password'     => bcrypt("password"),
                'phone_number' => "0000000010",
                'created_at'   => Carbon::now('UTC'),
                'updated_at'   => Carbon::now('UTC'),
            ],
            [
                'StudentID'    => 'STU002',
                'name'         => "Flora Rose",
                'email'        => "rose@gmail.com",
                'password'     => bcrypt("password"),
                'phone_number' => "0000000011",
                'created_at'   => Carbon::now('UTC'),
                'updated_at'   => Carbon::now('UTC'),
            ],
            [
                'StudentID'    => 'STU003',
                'name'         => "Mavis Jullius",
                'email'        => "jullius@gmail.com",
                'password'     => bcrypt("password"),
                'phone_number' => "0000000012",
                'created_at'   => Carbon::now('UTC'),
                'updated_at'   => Carbon::now('UTC'),
            ],
            [
                'StudentID'    => 'STU004',
                'name'         => "Mars",
                'email'        => "mars@gmail.com",
                'password'     => bcrypt("password"),
                'phone_number' => "0000000013",
                'created_at'   => Carbon::now('UTC'),
                'updated_at'   => Carbon::now('UTC'),
            ],
            [
                'StudentID'    => 'STU005',
                'name'         => "Mr.Jupitor",
                'email'        => "jupitor@gmail.com",
                'password'     => bcrypt("password"),
                'phone_number' => "0000000014",
                'created_at'   => Carbon::now('UTC'),
                'updated_at'   => Carbon::now('UTC'),
            ],
            [
                'StudentID'    => 'STU006',
                'name'         => "Snow Win",
                'email'        => "snow@gmail.com",
                'password'     => bcrypt("password"),
                'phone_number' => "0000000015",
                'created_at'   => Carbon::now('UTC'),
                'updated_at'   => Carbon::now('UTC'),
            ],
            [
                'StudentID'    => 'STU007',
                'name'         => "Mis.Sunflower",
                'email'        => "sunflower@gmail.com",
                'password'     => bcrypt("password"),
                'phone_number' => "0000000016",
                'created_at'   => Carbon::now('UTC'),
                'updated_at'   => Carbon::now('UTC'),
            ],
            [
                'StudentID'    => 'STU008',
                'name'         => "Jasmine",
                'email'        => "jasmine@gmail.com",
                'password'     => bcrypt("password"),
                'phone_number' => "0000000017",
                'created_at'   => Carbon::now('UTC'),
                'updated_at'   => Carbon::now('UTC'),
            ],
            [
                'StudentID'    => 'STU009',
                'name'         => "Shinny",
                'email'        => "shinny@gmail.com",
                'password'     => bcrypt("password"),
                'phone_number' => "0000000018",
                'created_at'   => Carbon::now('UTC'),
                'updated_at'   => Carbon::now('UTC'),
            ],
            [
                'StudentID'    => 'STU010',
                'name'         => "Shane",
                'email'        => "shane@gmail.com",
                'password'     => bcrypt("password"),
                'phone_number' => "0000000019",
                'created_at'   => Carbon::now('UTC'),
                'updated_at'   => Carbon::now('UTC'),
            ],
        ];

        foreach ($students as $studentData) {
            $student = Student::create($studentData);
            // Assign the "student" role
            $student->assignRole('student');
        }
    }
}
