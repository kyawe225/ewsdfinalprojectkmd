<?php
namespace Database\Seeders;

use App\Models\Allocation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AllocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remove all existing allocation records
        Allocation::truncate();

        // Loop from student_id 2 up to 20 (inclusive)
        for ($studentId = 31; $studentId <= 40; $studentId++) {
            Allocation::create([
                'student_id'      => $studentId,
                'tutor_id'        => 2,
                'staff_id'        => 1,
                'allocated_by'    => 'Kyawe',
                'allocation_date' => Carbon::now('UTC')->toDateString(),
            ]);
        }
    }
}
