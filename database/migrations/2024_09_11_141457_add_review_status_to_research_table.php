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
        Schema::table('research', function (Blueprint $table) {
            $table->enum('review_status', ['Needs Changes', 'Under Review', 'Rejected', 'Forwarded to Reviewer', 'Forwarded to Approver', 'Forwarded to Publisher'])
                  ->nullable()
                  ->after('research_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research', function (Blueprint $table) {
            //
        });
    }
};
