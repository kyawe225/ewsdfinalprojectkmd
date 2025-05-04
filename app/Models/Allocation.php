<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Allocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'allocations'; //  Explicit table name

    protected $fillable = [
        'allocation_date',
        'allocated_by',
        'staff_id',
        'tutor_id',
        'student_id',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'tutor_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}


