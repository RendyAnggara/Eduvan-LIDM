<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $table = 'student_answers';

    protected $fillable = [
        'quiz_result_id',
        'user_id',
        'question_id',
        'answer_text',
        'is_correct',
        'score',
        'teacher_note',
    ];

    public function quizResult()
    {
        return $this->belongsTo(QuizResult::class, 'quiz_result_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
