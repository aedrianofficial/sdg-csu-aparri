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
            

            // Add foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->unsigned;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['user_id']);
            
            // Drop the user_id column
            $table->dropColumn('user_id');
        });
    }
};
