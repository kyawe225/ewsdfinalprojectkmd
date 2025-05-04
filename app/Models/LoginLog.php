<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'userable_id',
        'userable_type',
        'browser',
        'ip_address',

    ];

    public function userable()
    {
        return $this->morphTo();
    }
}
