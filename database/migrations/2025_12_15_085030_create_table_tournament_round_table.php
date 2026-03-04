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
        Schema::create('tournament_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');
            $table->unsignedSmallInteger('round_number')->default(1);
            $table->time('start_interval', 0)->default('00:00:00');
            $table->time('morning', 0)->default('00:00:00');
            $table->time('afternoon', 0)->default('00:00:00');
            $table->time('crossover_one', 0)->default('00:00:00');
            $table->time('crossover_ten', 0)->default('00:00:00');
            $table->date('date')->nullable();
            $table->dateTime('action_date')->nullable();
            $table->string('timezone')->default('Asia/Jakarta');
            $table->unsignedSmallInteger('ball')->default(2);
            $table->enum('transportation', ['cart', 'combine', 'walk'])->default('cart');
            $table->enum('status', ['setup', 'group', 'pace', 'referee', 'active', 'pause', 'finish'])->default('setup');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_rounds');
    }
};
