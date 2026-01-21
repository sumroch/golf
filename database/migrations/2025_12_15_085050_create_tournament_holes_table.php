<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tournament_holes', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('number')->default(1);
            $table->time('allowed_time', 0)->default('00:00:00');
            $table->unsignedSmallInteger('par')->default(0);
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('tournament_round_id')->constrained('tournament_rounds')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_holes');
    }
};
