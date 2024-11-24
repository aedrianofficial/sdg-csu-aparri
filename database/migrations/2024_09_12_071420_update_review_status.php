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
            $table->enum('review_status', ['Needs Changes', 'Rejected', 'Forwarded to Reviewer', 'Forwarded to Approver', 'Forwarded to Publisher'])
                  ->nullable()
                  ->change();
        });

        // Revert the enum change in the reports table
        Schema::table('reports', function (Blueprint $table) {
            $table->enum('review_status', ['Needs Changes', 'Rejected', 'Forwarded to Reviewer', 'Forwarded to Approver', 'Forwarded to Publisher'])
                  ->nullable()
                  ->change();
        });

        // Revert the enum change in the research table
        Schema::table('research', function (Blueprint $table) {
            $table->enum('review_status', ['Needs Changes', 'Rejected', 'Forwarded to Reviewer', 'Forwarded to Approver', 'Forwarded to Publisher'])
                  ->nullable()
                  ->change();
        });
     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
