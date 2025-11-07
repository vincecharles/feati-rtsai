<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'student_number',
        'enrollment_date',
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'sex',
        'date_of_birth',
        'place_of_birth',
        'civil_status',
        'nationality',
        'mobile',
        'current_address',
        'permanent_address',
        'emergency_name',
        'emergency_relationship',
        'emergency_phone',
        'emergency_address',
        'department',
        'program',
        'course',
        'year_level',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
