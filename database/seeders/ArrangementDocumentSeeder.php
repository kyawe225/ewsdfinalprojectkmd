<?php
namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArrangementDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $statuses     = ['canceled', 'accepted', 'finished', 'watched'];
        $createdTypes = ['tutor', 'student'];

        for ($i = 1; $i <= 10; $i++) {
            DB::table('arrangement_document')->insert([
                'assignment_arrangement_id' => 3,
                // 'feedback'                  => 'Feedback for arrangement document ' . $i,
                'document_id'               => rand(11, 20),
                'created_by'                => 2,
                'created_type'              => 'student',
                // 'status'                    => $statuses[array_rand($statuses)],
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
            ]);
        }
    }
}
