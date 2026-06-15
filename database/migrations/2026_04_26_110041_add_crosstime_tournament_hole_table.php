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
        Schema::table('tournament_holes', function (Blueprint $table) {
            $table->time('crosstime', 0)->default('00:00:00')->nullable()->after('par');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournament_holes', function (Blueprint $table) {
            $table->dropColumn('crosstime');
        });
    }
};
