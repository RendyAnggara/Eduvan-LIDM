<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $blueprint) {
            $blueprint->dropForeign('quizzes_chapter_id_foreign');
        });

        Schema::table('quizzes', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['chapter_id', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'answer']);

            $blueprint->string('title')->after('course_id');
            $blueprint->integer('time_limit')->default(30)->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['title', 'time_limit']);

            $blueprint->bigInteger('chapter_id')->unsigned()->nullable()->after('course_id');
            $blueprint->string('question')->after('chapter_id');
            $blueprint->string('option_a')->after('question');
            $blueprint->string('option_b')->after('option_a');
            $blueprint->string('option_c')->after('option_b');
            $blueprint->string('option_d')->after('option_c');
            $blueprint->char('answer', 1)->after('option_d');
            $blueprint->foreign('chapter_id')->references('id')->on('chapters')->onDelete('cascade');
        });
    }
};
