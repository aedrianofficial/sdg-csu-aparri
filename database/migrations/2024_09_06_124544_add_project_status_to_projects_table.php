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
            $table->enum('project_status', ['Proposed', 'On-Going', 'On-Hold', 'Completed', 'Rejected'])
            ->default('Proposed')
            ->notNull()
            ->after('description'); // Position after the 'description' column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('project_status');
        });
    }
};
