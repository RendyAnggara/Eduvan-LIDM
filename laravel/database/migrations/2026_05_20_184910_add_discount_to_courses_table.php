<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountToCoursesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 🌟 Langsung tembak Schema tanpa dibungkus Route::middleware lek!
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('discount')->default(0)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
}
