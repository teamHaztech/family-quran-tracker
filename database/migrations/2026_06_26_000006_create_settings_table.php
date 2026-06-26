<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('family_name')->default('My Family');
            $table->string('family_logo')->nullable();
            $table->string('theme')->default('light');           // light | dark
            $table->unsignedInteger('daily_goal_pages')->default(5);
            $table->unsignedInteger('monthly_goal_pages')->default(150);
            $table->boolean('enable_leaderboard')->default(true);
            $table->boolean('enable_badges')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
