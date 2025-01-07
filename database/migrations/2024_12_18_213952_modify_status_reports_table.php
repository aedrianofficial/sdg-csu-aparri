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
             // Remove the files_link column
             $table->dropColumn('files_link');

            // Add related_link as a VARCHAR and status_report_file as BLOB
            $table->string('related_link')->nullable()->after('remarks');
            $table->binary('status_report_file')->nullable()->after('related_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('status_reports', function (Blueprint $table) {
            // Re-add the files_link column
            $table->string('files_link')->nullable()->after('remarks');

            // Drop the new columns
            $table->dropColumn('related_link');
            $table->dropColumn('status_report_file');
        });
    }
};
