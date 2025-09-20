<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email', 
        'password',
        'role_id',
        'status' // Add this
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class)->withDefault(['label' => '-']);
    }

    public function profile()
    {
        return $this->hasOne(EmployeeProfile::class)->withDefault();
    }

    public function dependents()
    {
        return $this->hasMany(Dependent::class);
    }
}