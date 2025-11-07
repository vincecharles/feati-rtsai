<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model {
    protected $fillable = [
        'user_id','employee_number','date_hired',
        'last_name','first_name','middle_name','suffix',
        'preferred_name','sex','age','date_of_birth','place_of_birth',
        'civil_status','nationality','mobile','email',
        'current_address','permanent_address',
        'emergency_name','emergency_relationship','emergency_phone','emergency_address',
        'department','position',
    ];
    
    protected $casts = [
        'date_of_birth' => 'date',
        'date_hired' => 'date',
    ];
    
    public function user(){ return $this->belongsTo(User::class); }
}
