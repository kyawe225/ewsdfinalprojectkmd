<?php
namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogDocument;
use App\Models\Comments;
use Illuminate\Database\Seeder;

class BlogsAndCommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder creates 10 sample blogs. For each blog,
     * it stores one document with a fixed file name and
     * creates a random number of comments.
     */
    public function run()
    {
        // Loop to create 10 blogs
        for ($i = 1; $i <= 10; $i++) {

            // Alternate author roles between 'student' and 'tutor'
            $role = ($i % 1 === 0) ? 'student' : 'tutor';

            // Create a blog entry
            $blog = Blog::create([
                'title'       => 'Sample Blog Title ' . $i,
                'content'     => 'This is the content for sample blog number ' . $i,
                'author'      => 'Author ' . $i,
                'author_role' => $role,
                // Using random numbers for student_id and tutor_id for demonstration.
                'student_id'  => ($role === 'student') ? rand(1, 5) : rand(6, 10),
                'tutor_id'    => ($role === 'tutor') ? rand(6, 10) : rand(1, 5),
            ]);

            // Store a related document for the blog using a fixed file name
            BlogDocument::create([
                'blog_id'          => $blog->id,
                'BlogDocumentFile' => 'documents/68hZO0ksoHIBdNkr7yXjnTU4XsSQAjfbFBMxgOeS.pdf',
            ]);

            // Determine a random number of comments to add (between 1 and 5)
            $numberOfComments = rand(1, 5);

            // Create the comments for this blog
            for ($j = 1; $j <= $numberOfComments; $j++) {

                // If the blog author is a student, create comments from tutors and vice versa.
                $commentData = [
                    'blog_id' => $blog->id,
                    'content' => 'This is comment ' . $j . ' for blog ' . $i,
                ];

                if ($role === 'student') {
                    $commentData['tutor_id']   = rand(6, 10);
                    $commentData['student_id'] = null;
                } else {
                    $commentData['student_id'] = rand(1, 5);
                    $commentData['tutor_id']   = null;
                }

                Comments::create($commentData);
            }
        }
    }
}
