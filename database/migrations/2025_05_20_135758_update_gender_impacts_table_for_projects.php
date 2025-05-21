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
        Schema::table('gender_impacts', function (Blueprint $table) {
            // Make research_id nullable
            $table->unsignedBigInteger('research_id')->nullable()->change();
            
            // Add project_id
            $table->unsignedBigInteger('project_id')->nullable()->after('research_id');
            
            // Add foreign key for project_id
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gender_impacts', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['project_id']);
            
            // Drop the project_id column
            $table->dropColumn('project_id');
            
            // Make research_id non-nullable again
            $table->unsignedBigInteger('research_id')->nullable(false)->change();
        });
    }
};
