<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'description',
        'uploaded_by',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}