<?php
namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        //$createdTypes = ['tutor', 'student'];

        for ($i = 1; $i <= 10; $i++) {
            DB::table('document')->insert([
                'file'         => 'document_' . $i . '.pdf',
                'file_name'    => 'Document ' . $i,
                'created_by'   => 2,
                'created_type' => 'student',
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]);
        }
    }
}
