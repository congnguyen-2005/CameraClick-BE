<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    // 👇 CHỐT CHẶN QUAN TRỌNG: Ép Laravel dùng đúng bảng ntc_users
    protected $table = 'ntc_users';

    public $timestamps = true; 

    protected $fillable = [
        'name', 'email', 'phone', 'username', 'password', 'roles', 
        'avatar', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'status' => 'integer',
    ];
}