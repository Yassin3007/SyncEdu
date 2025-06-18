<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'title',
        'description',
        'duration_minutes',
        'total_marks',
        'is_active',
        'start_time',
        'end_time',
        'created_by',
        'subject_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'total_marks' => 'decimal:2'
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function calculateTotalMarks()
    {
        $this->total_marks = $this->questions()->sum('marks');
        $this->save();
    }

    public function isAvailable(): bool
    {
        if (!$this->is_active) return false;

        $now = now();
        if ($this->start_time && $now->lt($this->start_time)) return false;
        if ($this->end_time && $now->gt($this->end_time)) return false;

        return true;
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class,'subject_id');
    }
}
