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
        Schema::table('projects', function (Blueprint $table) {
            // Add new columns after 'description'
            $table->text('review_feedback')->nullable()->after('description');
            $table->enum('review_status', ['Needs Changes','Rejected', 'Forwarded to Reviewer', 'Forwarded to Approver', 'Forwarded to Publisher'])->nullable()->after('review_feedback');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void

    {
        Schema::table('projects', function (Blueprint $table) {
            // Remove columns in reverse order
            $table->dropColumn('review_status');
            $table->dropColumn('review_feedback');
        });
    }
};
