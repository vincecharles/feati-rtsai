<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViolationNote extends Model
{
    protected $fillable = [
        'violation_id',
        'user_id',
        'note',
    ];

    public function violation()
    {
        return $this->belongsTo(Violation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
