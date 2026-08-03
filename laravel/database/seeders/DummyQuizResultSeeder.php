<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Quiz;
use App\Models\QuizResult;
use App\Models\Question;
use App\Models\StudentAnswer;

class DummyQuizResultSeeder extends Seeder
{
    public function run()
    {
        $student = User::where('role', 'student')->first();
        $quiz = Quiz::with('questions')->first();

        if (!$student || !$quiz) {
            $this->command->error('Data siswa atau kuis belum ada!');
            return;
        }

        QuizResult::where('user_id', $student->id)->where('course_id', $quiz->course_id)->delete();

        $result = QuizResult::create([
            'user_id' => $student->id,
            'course_id' => $quiz->course_id,
            'score' => 50,
            'status' => 'Belum Dinilai'
        ]);

        foreach ($quiz->questions as $question) {
            if ($question->type === 'essay') {
                StudentAnswer::create([
                    'quiz_result_id' => $result->id,
                    'user_id' => $student->id,
                    'question_id' => $question->id,
                    'answer_text' => 'Ini adalah jawaban essay dummy dari siswa untuk dites oleh guru di halaman review.',
                    'is_correct' => 0,
                    'score' => 0
                ]);
            } else {
                StudentAnswer::create([
                    'quiz_result_id' => $result->id,
                    'user_id' => $student->id,
                    'question_id' => $question->id,
                    'answer_text' => $question->correct_answer ?? 'A',
                    'is_correct' => 1,
                    'score' => 100
                ]);
            }
        }

        $this->command->info('Data dummy kuis & jawaban siswa berhasil dibuat!');
    }
}
