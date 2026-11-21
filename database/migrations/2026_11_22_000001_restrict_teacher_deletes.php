<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // A teacher's grades, quizzes and modules must not vanish when the teacher account is deleted.
    private array $tables = ['grades', 'quizzes', 'modules'];

    public function up(): void
    {
        foreach ($this->tables as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropForeign(['teacher_id']);
                $table->foreign('teacher_id')->references('id')->on('users')->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropForeign(['teacher_id']);
                $table->foreign('teacher_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }
};
