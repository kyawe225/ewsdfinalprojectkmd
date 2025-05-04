<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Student extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $guard_name = 'api';
    protected $table = 'students';

    protected $fillable = [
        'StudentID',
        'name',
        'email',
        'password',
        'phone_number',
        'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function allocations()
    {
        return $this->hasMany(Allocation::class, 'student_id');
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'student_id');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'student_id');
    }

    public function arrangings()
    {
        return $this->hasMany(Arranging::class, 'student_id');
    }

    public function loginLogs()
    {
        return $this->morphMany(\App\Models\LoginLog::class, 'userable');
    }
}
