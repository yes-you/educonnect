<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // When the user last opened the Announcements page. Announcements newer than this show as "new" in the navbar.
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('announcements_seen_at')->nullable()->after('grade_level');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('announcements_seen_at');
        });
    }
};
