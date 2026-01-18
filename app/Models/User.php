<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'social_id',
        'social_type',
        'name',
        'email',
        'password',
        'role',          // Role: admin/employee
        'leave_quota',   // Default 12 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi: Satu User bisa memiliki banyak pengajuan cuti
    public function leaveRequests()
    {
        return $this->hasMany(datacuti::class);
    }
}
