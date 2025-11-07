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
        'student_id',
        'program',
        'year_level'
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
            'student_id' => $this->student_id,
            'program' => $this->program,
            'year_level' => $this->year_level,
            'status' => $this->status,
            'role' => $this->role?->name,
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

    public function dependents()
    {
        return $this->hasMany(Dependent::class);
    }

    public function violations()
    {
        return $this->hasMany(Violation::class, 'student_id');
    }
}