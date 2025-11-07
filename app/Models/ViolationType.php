<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViolationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'offense_class',
        'penalties',
    ];

    protected $casts = [
        'penalties' => 'array',
    ];

    // Relationships
    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }

    // Scopes
    public function scopeMajor($query)
    {
        return $query->where('category', 'major');
    }

    public function scopeMinor($query)
    {
        return $query->where('category', 'minor');
    }

    public function scopeByClass($query, $class)
    {
        return $query->where('offense_class', $class);
    }
    
    // Helper methods
    public function getPenalty($offenseNumber)
    {
        $offenseNumber = (string)$offenseNumber;
        return $this->penalties[$offenseNumber] ?? 'Not specified';
    }
    
    public function getFullCode()
    {
        return "Code {$this->code} - {$this->name}";
    }
}
