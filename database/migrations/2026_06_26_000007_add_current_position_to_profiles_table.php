<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('current_surah')->nullable()->after('daily_goal_pages');
            $table->unsignedSmallInteger('current_ayah')->nullable()->after('current_surah');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['current_surah', 'current_ayah']);
        });
    }
};
