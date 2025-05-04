<?php
namespace Database\Seeders;

use App\Models\Allocation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestingSeederForAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder creates allocation records for student IDs 4 through 20.
     * Each allocation will have an allocation date set to 14 days in the past.
     *
     * @return void
     */
    public function run()
    {
        // Calculate the allocation date as 14 days before now
        $allocationDate = Carbon::now()->subDays(40);

        // Loop through student IDs 4 to 20
        for ($studentId = 4; $studentId <= 20; $studentId++) {
            Allocation::create([
                'allocation_date' => $allocationDate,
                // You can adjust the allocated_by, staff_id, and tutor_id as needed
                'allocated_by'    => 1,
                'staff_id'        => 1,
                'tutor_id'        => 1,
                'student_id'      => $studentId,
            ]);
        }
    }
}
