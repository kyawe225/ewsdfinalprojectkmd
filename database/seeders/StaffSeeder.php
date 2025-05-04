<?php
namespace Database\Seeders;

use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Staff::truncate();

        $staffMembers = [
            [
                "name"         => "Doris Navarro3",
                "email"        => "doris34@gmail.com",
                "password"     => bcrypt("password"),
                "phone_number" => "09790000000",
            ],
            [
                "name"         => "Joanne Duke",
                "email"        => "joanne3@gmail.com",
                "password"     => bcrypt("password"),
                "phone_number" => "09790000004",
            ],
            [
                "name"         => "Alden Beck",
                "email"        => "doris333@gmail.com",
                "password"     => bcrypt("password"),
                "phone_number" => "09790000003",
            ],
            [
                "name"         => "Juanita Baird",
                "email"        => "juanita3@gmail.com",
                "password"     => bcrypt("password"),
                "phone_number" => "09790000001",
            ],
            [
                "name"         => "Wallace Cowan",
                "email"        => "wallace3@gmail.com",
                "password"     => bcrypt("password"),
                "phone_number" => "09790000002",
            ],
        ];

        foreach ($staffMembers as $staffData) {

            $staffData['created_at'] = Carbon::now('UTC');
            $staffData['updated_at'] = Carbon::now('UTC');

            $staff = Staff::create($staffData);
            // Assign the "staff" role to the staff member
            $staff->assignRole('staff');
        }
    }
}
