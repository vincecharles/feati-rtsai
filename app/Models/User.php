<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
    use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Searchable;

    protected $fillable = [
        'name',
        'email', 
        'password',
        'role_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'role' => $this->role?->name,
            'employee_number' => $this->profile?->employee_number,
            'student_number' => $this->studentProfile?->student_number,
            'department' => $this->profile?->department ?? $this->studentProfile?->department,
            'position' => $this->profile?->position,
            'mobile' => $this->profile?->mobile ?? $this->studentProfile?->mobile,
            'first_name' => $this->profile?->first_name ?? $this->studentProfile?->first_name,
            'last_name' => $this->profile?->last_name ?? $this->studentProfile?->last_name,
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class)->withDefault(['label' => '-']);
    }

    public function profile()
    {
        return $this->hasOne(EmployeeProfile::class)->withDefault();
    }

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class)->withDefault();
    }

    public function violations()
    {
        return $this->hasMany(Violation::class, 'student_id');
    }
}