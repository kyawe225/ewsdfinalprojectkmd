<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentArrangement extends Model
{
    use HasFactory;

    protected $table = 'assignment_arrangements';

    protected $fillable = [
        'title',
        'instructions',
        'content',
        'arrange_id',
        'feedback',
        'dead_line',
        'status',
    ];

    public function arranging()
    {
        return $this->belongsTo(Arranging::class, 'arrange_id');
    }

    public function documents()
    {
        return $this->belongsToMany(
            Document::class,
            'arrangement_document',
            'assignment_arrangement_id',
            'document_id'
        );
    }
}
