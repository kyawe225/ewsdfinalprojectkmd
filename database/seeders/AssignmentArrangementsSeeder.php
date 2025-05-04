<?php
namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignmentArrangementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('assignment_arrangements')->insert([
            [
                'title'        => 'Math Assignment',
                'instructions' => 'Solve all the problems in the attached document.',
                'content'      => 'Detailed content for the math assignment.',
                'arrange_id'   => 2, // Make sure this id exists in the arrangings table
                'feedback'     => 'Good work, but review question 3 for improvements.',
                'dead_line'    => $now->copy()->addDays(7),
                'status'       => 'accepted', // One of: canceled, accepted, finished, watched
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'title'        => 'Science Project',
                'instructions' => 'Research on renewable energy sources and provide a summary.',
                'content'      => 'Content for the science project goes here.',
                'arrange_id'   => 2, // Adjust according to your arrangings data
                'feedback'     => 'Include more details on solar and wind energy.',
                'dead_line'    => $now->copy()->addDays(10),
                'status'       => 'watched',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ]);
    }
}
