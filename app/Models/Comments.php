<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use HasFactory;

    // Specify the table name (Laravel assumes 'comments' automatically)
    protected $table = 'comments';

    // Allow mass assignment for these fields
    protected $fillable = [
        'content',
        'blog_id',
        'student_id',
        'tutor_id'
    ];

    /**
     * Define the relationship with the Blog model
     */
    public function blog()
    {
        return $this->belongsTo(\App\Models\Blog::class, 'blog_id');
    }

    /**
     * Define the relationship with the Student model (nullable)
     */
    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    /**
     * Define the relationship with the Tutor model (nullable)
     */
    public function tutor()
    {
        return $this->belongsTo(\App\Models\Tutor::class, 'tutor_id');
    }
}
