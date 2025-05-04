<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arranging extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'tutor_id',
        'status',
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

    public function meetingDetails()
    {
        return $this->hasMany(MeetingDetail::class, 'arrange_id');
    }

    public function meetingRequests()
    {
        return $this->hasMany(MeetingRequest::class, 'approved_arrange_id');
    }

    
}
