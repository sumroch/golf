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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('location', 64);
            $table->string('organizer', 128);
            $table->date('date_start');
            $table->unsignedSmallInteger('round')->default(0);
            $table->string('timezone')->default('Asia/Jakarta');
            $table->enum('status', ['created', 'setup', 'active', 'finish'])->default('created');

            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
