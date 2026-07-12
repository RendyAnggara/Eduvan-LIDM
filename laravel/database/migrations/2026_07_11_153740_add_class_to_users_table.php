<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('class', ['Kelas 7', 'Kelas 8', 'Kelas 9'])->nullable()->after('school_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop kolom jika migration di-rollback
            $table->dropColumn('class');
        });
    }
};
