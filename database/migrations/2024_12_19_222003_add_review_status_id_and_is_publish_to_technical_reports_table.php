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
        Schema::table('terminal_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('review_status_id')->nullable()->after('abstract'); // Add review_status_id column
            $table->boolean('is_publish')->default(0)->after('review_status_id'); // Add is_publish column
            
            // Add foreign key constraint
            $table->foreign('review_status_id')->references('id')->on('review_statuses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terminal_reports', function (Blueprint $table) {
            $table->dropForeign(['review_status_id']); // Drop foreign key constraint
            $table->dropColumn('review_status_id'); // Drop review_status_id column
            $table->dropColumn('is_publish'); // Drop is_publish column
        });
    }
};
