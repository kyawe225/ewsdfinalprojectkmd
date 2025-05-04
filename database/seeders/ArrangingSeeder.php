<?php
namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArrangingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $statuses = ['pending', 'completed', 'canceled'];

        for ($i = 1; $i <= 20; $i++) {
            DB::table('arrangings')->insert([
                'student_id' => rand(1, 20),
                'tutor_id'   => rand(1, 20),
                'status'     => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
