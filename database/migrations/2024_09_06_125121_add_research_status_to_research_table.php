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
            $table->enum('research_status', ['Proposed', 'On-Going', 'On-Hold', 'Completed', 'Rejected'])
            ->default('Proposed')
            ->notNull()
            ->after('description'); // Place it before the 'description' column;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research', function (Blueprint $table) {
            $table->dropColumn('research_status');
        });
    }
};
