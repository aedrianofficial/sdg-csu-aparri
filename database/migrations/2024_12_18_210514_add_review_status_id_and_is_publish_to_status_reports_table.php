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
            //
             // Add review_status_id as an unsigned big integer and nullable
             $table->unsignedBigInteger('review_status_id')->nullable()->after('logged_by_id');

             // Add is_publish as a boolean with a default value of false
             $table->boolean('is_publish')->default(false)->after('review_status_id');
 
             // If you want to add a foreign key constraint for review_status_id
             $table->foreign('review_status_id')->references('id')->on('review_statuses')->onDelete('set null');
 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('status_reports', function (Blueprint $table) {
            //
            // Drop foreign key if it exists
            $table->dropForeign(['review_status_id']);
            // Drop the columns
            $table->dropColumn('review_status_id');
            $table->dropColumn('is_publish');
        });
    }
};
