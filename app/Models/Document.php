<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    // Explicit table name if needed.
    protected $table = 'document';

    protected $fillable = [
        'file',
        'file_name',
        'created_by',
        'created_type',
    ];

    /**
     * Get all the arrangement documents associated with this document.
     */
    public function arrangementDocuments()
    {
        return $this->hasMany(ArrangementDocument::class, 'document_id');
    }

    /**
     * Get all assignment arrangements associated with this document
     * via the pivot table 'arrangement_document'.
     */
    public function assignmentArrangements()
    {
        return $this->belongsToMany(
            AssignmentArrangement::class,
            'arrangement_document',
            'document_id',
            'assignment_arrangement_id'
        );
    }
}
