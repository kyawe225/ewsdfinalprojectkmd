<?php
namespace Database\Seeders;

use App\Models\Blog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestingBlogSeederForAdminReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder creates a blog record for each student with IDs from 4 to 20.
     * The 'tutor_id' is set to 1.
     * The blog's created_at (and updated_at) date is randomly set to a day
     * between 14 and 20 days ago.
     *
     * @return void
     */
    public function run()
    {
        for ($studentId = 4; $studentId <= 20; $studentId++) {
            // Randomly choose a number of days between 14 and 20.
            $daysAgo = rand(14, 20);

            // Get the date by subtracting the number of days from now.
            $randomDate = Carbon::now()->subDays($daysAgo);

            Blog::create([
                'author'      => 'Test Author',
                'author_role' => 'Student',
                'title'       => "Sample Blog Title for Student ID {$studentId}",
                'content'     => "This is sample blog content for student ID {$studentId}.",
                'tutor_id'    => 1,
                'student_id'  => $studentId,
                // Set created_at and updated_at dynamically.
                'created_at'  => $randomDate,
                'updated_at'  => $randomDate,
            ]);
        }
    }
}
