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
        Schema::table('employees', function (Blueprint $table) {
            // Remove Kenyan field
            $table->dropColumn('kra_pin');

            // Add Malaysian identification fields
            $table->string('nric_number')->nullable()->after('national_id');
            $table->string('income_tax_number')->nullable()->after('nric_number');
            $table->string('epf_number')->nullable()->after('income_tax_number');
            $table->string('socso_number')->nullable()->after('epf_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Remove Malaysian fields
            $table->dropColumn([
                'nric_number',
                'income_tax_number',
                'epf_number',
                'socso_number',
            ]);

            // Restore Kenyan field
            $table->string('kra_pin')->nullable();
        });
    }
};
