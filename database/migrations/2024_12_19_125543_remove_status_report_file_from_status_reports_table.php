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
        Schema::table('status_reports', function (Blueprint $table) {
            $table->dropColumn('status_report_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('status_reports', function (Blueprint $table) {
            //
            Schema::table('status_reports', function (Blueprint $table) {
                $table->string('status_report_file')->nullable(); // Add back the column if rolling back
            });
        });
    }
};
