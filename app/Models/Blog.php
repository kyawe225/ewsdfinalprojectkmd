<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $guarded  = [];
    protected $fillable = [
        'author',
        'author_role',
        'title',
        'content',
        'tutor_id',
        'student_id',
    ];

    /**
     * Get the tutor associated with the blog.
     */
    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'tutor_id');
    }

    /**
     * Get the student associated with the blog.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the comments for the blog.
     */
    public function comments()
    {
        return $this->hasMany(Comments::class);
    }

    /**
     * Get the documents associated with the blog.
     */
    public function documents()
    {
        return $this->hasMany(BlogDocument::class, 'blog_id');
    }
}
