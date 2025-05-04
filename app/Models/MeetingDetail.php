<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "meeting_detail"; // Kept singular as per your requirement

    protected $fillable = [
        'arrange_date',
        'meeting_type',
        'location',
        'meeting_link',
        'online_meeting_application_type',
        'arrange_id',
        'topic',
        'description', // Added description
        'status',
    ];

    protected $casts = [
        'arrange_date' => 'datetime',
    ];

    // Relationships
    public function arranging()
    {
        return $this->belongsTo(Arranging::class, 'arrange_id');
    }

    public function meetingRecords()
    {
        return $this->hasMany(MeetingRecord::class, 'meeting_detail_id');
    }
}
