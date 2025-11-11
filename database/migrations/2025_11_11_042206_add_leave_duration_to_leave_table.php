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
        Schema::table('leave', function (Blueprint $table) {
            $table->enum('leave_duration', ['Full Day', 'Half Day - Morning', 'Half Day - Evening'])
                ->default('Full Day')
                ->after('leave_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave', function (Blueprint $table) {
            $table->dropColumn('leave_duration');
        });
    }
};
