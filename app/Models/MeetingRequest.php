<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingRequest extends Model
{
    use HasFactory;

    protected $table = "meeting_request";

    protected $fillable = [
        'student_id',
        'tutor_id',
        'reason',
        'arrange_date',
        'approved',
        'topic',
        'meeting_type',
        'location',
        'reject_reason',
        'approved_arrange_id',
        'approved_reject_date',
        'status'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function approvedArrangement()
    {
        return $this->belongsTo(Arranging::class, 'approved_arrange_id');
    }
}
