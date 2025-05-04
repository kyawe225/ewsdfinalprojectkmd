<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArrangementDocument extends Model
{
    protected $table = 'arrangement_document';

    protected $primaryKey = 'id';

    protected $fillable = [
        'assignment_arrangement_id',
        'document_id',
        'created_by',
        'created_type',
        'arrangement_type',
    ];

    /**
     * Get the assignment arrangement for this pivot record.
     */
    public function assignmentArrangement()
    {
        return $this->belongsTo(AssignmentArrangement::class, 'assignment_arrangement_id');
    }

    /**
     * Get the document for this pivot record.
     */
    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }
}
