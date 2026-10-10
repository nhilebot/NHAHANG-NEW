<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'name', 'email', 'phone', 'password',
    'address', 'city', 'avatar', 'shifts'
];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', 'shifts' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
 public function role()
{
    // Sử dụng \App\Models\Role::class để Laravel chỉ định đúng file Role.php
    return $this->belongsTo(\App\Models\Role::class, 'role_id');
}
public function isStaff(): bool { return (int) $this->role_id === Role::STAFF; }
public function isChef(): bool { return (int) $this->role_id === Role::CHEF; }
public function isAdmin()
{
    return (int) $this->role_id === Role::ADMIN;
} // Đảm bảo có dấu đóng này
}