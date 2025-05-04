<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('blogs')->insert([
            [
                'title'       => 'Introduction to Laravel',
                'content'     => 'This blog post covers the basics of Laravel framework.',
                'author'      => 'John Doe',
                'student_id'  => 15, // Please don't set null. Student Id is require for blog
                'tutor_id'    => 3,  // Please don't set null. Tutor Id is require for blog
                'author_role' => 'student',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Best Practices in PHP',
                'content'     => 'This blog post covers PHP coding standards and best practices.',
                'author'      => 'Jane Smith',
                'student_id'  => 15,
                'tutor_id'    => 3,
                'author_role' => 'tutor',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Building Secure APIs',
                'content'     => 'This blog post explains how to secure Laravel APIs using authentication and authorization.',
                'author'      => 'Alice Johnson',
                'student_id'  => 15,
                'tutor_id'    => 3,
                'author_role' => 'student',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
