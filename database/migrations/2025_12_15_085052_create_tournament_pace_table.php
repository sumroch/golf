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
        Schema::create('tournament_paces', function (Blueprint $table) {
            $table->id();
            $table->string('time', 16)->default('00:00');
            $table->enum('type', ['tee', 'crossover'])->default('tee');
            $table->datetime('finish_at')->nullable();
            $table->enum('status', ['created', 'progress', 'unmonitored', 'finish'])->default('created');
            $table->string('notes')->nullable();

            $table->foreignId('tournament_round_id')->constrained('tournament_rounds')->onDelete('cascade');
            $table->foreignId('hole_id')->constrained('tournament_holes')->onDelete('cascade');
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_paces');
    }
};
