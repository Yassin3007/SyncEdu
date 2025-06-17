<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const TYPE_WRITTEN = 'written';
    const TYPE_TRUE_FALSE = 'true_false';

    protected $fillable = [
        'exam_id',
        'type',
        'question_text',
        'marks',
        'options',
        'correct_answer',
        'order'
    ];

    protected $casts = [
        'options' => 'array',
        'marks' => 'decimal:2'
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function answerResults(): HasMany
    {
        return $this->hasMany(AnswerResult::class);
    }

    public function checkAnswer(string $studentAnswer): bool
    {
        switch ($this->type) {
            case self::TYPE_MULTIPLE_CHOICE:
            case self::TYPE_TRUE_FALSE:
                return strtolower(trim($studentAnswer)) === strtolower(trim($this->correct_answer));

            case self::TYPE_WRITTEN:
                // For written answers, you might want to implement more sophisticated checking
                // For now, exact match (case-insensitive)
                return strtolower(trim($studentAnswer)) === strtolower(trim($this->correct_answer));

            default:
                return false;
        }
    }
}
