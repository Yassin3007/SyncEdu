<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'started_at',
        'submitted_at',
        'total_score',
        'answers',
        'is_completed'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'answers' => 'array',
        'is_completed' => 'boolean',
        'total_score' => 'decimal:2'
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function answerResults(): HasMany
    {
        return $this->hasMany(AnswerResult::class);
    }

    public function isTimeUp(): bool
    {
        if (!$this->exam->duration_minutes) return false;

        $timeLimit = $this->started_at->addMinutes($this->exam->duration_minutes);
        return now()->gt($timeLimit);
    }

    public function getRemainingTimeAttribute(): int
    {
        if (!$this->exam->duration_minutes || $this->is_completed) return 0;

        $timeLimit = $this->started_at->addMinutes($this->exam->duration_minutes);
        $remaining = now()->diffInSeconds($timeLimit, false);

        return max(0, $remaining);
    }
}
