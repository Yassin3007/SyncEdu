<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnswerResult extends Model
{
    protected $fillable = [
        'exam_attempt_id',
        'question_id',
        'student_answer',
        'is_correct',
        'marks_obtained'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'marks_obtained' => 'decimal:2'
    ];

    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
