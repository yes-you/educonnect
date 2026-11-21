<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // A student may attempt a quiz only once. The database enforces it, so even a double click
    // on "Submit" cannot save a second attempt.
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->unique(['quiz_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropUnique(['quiz_id', 'user_id']);
        });
    }
};
