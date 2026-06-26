<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('surah')->nullable();
            $table->unsignedSmallInteger('start_page')->nullable();
            $table->unsignedSmallInteger('end_page')->nullable();
            $table->unsignedSmallInteger('pages_read')->default(0);
            $table->unsignedInteger('minutes_read')->default(0);
            $table->unsignedTinyInteger('juz')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->enum('method', ['manual', 'timer'])->default('manual');
            $table->timestamps();

            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_sessions');
    }
};
