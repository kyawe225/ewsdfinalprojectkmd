<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogDocument extends Model
{
    use HasFactory;

    protected $table = 'blog_documents';

    protected $fillable = [
        'blog_id',
        'BlogDocumentFile',
    ];

    /**
     * Get the blog that owns the document.
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }
}
