<?php
namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaticBlogSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // Insert a blog post quickly using a single DB query.
        DB::table('blogs')->insert([
            'author'      => 'Maryam Kulas',
            'author_role' => 'tutor',
            'title'       => 'Asking about Assignment',
            'content'     => 'This blog post by Tutor 2 for Student 31 includes multiple comments for discussion.',
            'tutor_id'    => 1,
            'student_id'  => 31,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        // Retrieve the last inserted blog's id.
        // (If lastInsertId doesn't work reliably with your PostgreSQL setup,
        //  you can also fetch the latest blog by ordering by id.)
        $blog   = DB::table('blogs')->orderBy('id', 'desc')->first();
        $blogId = $blog->id;

        // Prepare an array of 6 comments for the inserted blog.
        $comments = [];
        for ($i = 1; $i <= 6; $i++) {
            $comments[] = [
                'content'    => "Comment $i on Blog: Easy comment insertion.",
                'blog_id'    => $blogId,
                // Alternate between tutor and student comments:
                // Odd comments by Tutor 2 and even comments by Student 31.
                'tutor_id'   => ($i % 2 == 1) ? 2 : null,
                'student_id' => ($i % 2 == 0) ? 31 : null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        // Insert all comments in one bulk query.
        DB::table('comments')->insert($comments);
    }
}
