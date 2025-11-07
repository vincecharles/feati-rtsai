<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Violation extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'student_id',
        'reported_by',
        'offense_category',
        'violation_type',
        'sanction',
        'description',
        'status',
        'violation_date',
        'action_taken',
        'resolution_date',
    ];

    protected $casts = [
        'violation_date' => 'date',
        'resolution_date' => 'date',
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
            'student_name' => $this->student?->name,
            'student_number' => $this->student?->studentProfile?->student_number,
            'department' => $this->student?->studentProfile?->department,
            'violation_type' => $this->violation_type,
            'sanction' => $this->sanction,
            'description' => $this->description,
            'status' => $this->status,
            'violation_date' => $this->violation_date?->format('Y-m-d'),
            'reporter_name' => $this->reporter?->name,
        ];
    }

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ViolationType::class, 'violation_type_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ViolationNote::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function getSanctionLabelAttribute()
    {
        return match($this->sanction) {
            'Disciplinary Citation (E)' => 'Citation (E)',
            'Suspension (D)' => 'Suspension (D)',
            'Preventive Suspension (C)' => 'Prev. Suspension (C)',
            'Exclusion (B)' => 'Exclusion (B)',
            'Expulsion (A)' => 'Expulsion (A)',
            default => $this->sanction
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'under_review' => 'blue',
            'resolved' => 'green',
            'dismissed' => 'gray',
            default => 'gray'
        };
    }
}
