<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'feedback_id')) {
                $table->dropForeign(['feedback_id']); // Drop foreign key constraint
                $table->dropColumn('feedback_id');    // Drop the column
            }
        });

        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'feedback_id')) {
                $table->dropForeign(['feedback_id']); // Drop foreign key constraint
                $table->dropColumn('feedback_id');    // Drop the column
            }
        });

        Schema::table('research', function (Blueprint $table) {
            if (Schema::hasColumn('research', 'feedback_id')) {
                $table->dropForeign(['feedback_id']); // Drop foreign key constraint
                $table->dropColumn('feedback_id');    // Drop the column
            }
        });
    }

    /**
     * Reverse the migrations to add the feedback_id column back (if needed).
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('feedback_id')->nullable();
            $table->foreign('feedback_id')->references('id')->on('feedbacks')->onDelete('set null');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('feedback_id')->nullable();
            $table->foreign('feedback_id')->references('id')->on('feedbacks')->onDelete('set null');
        });

        Schema::table('research', function (Blueprint $table) {
            $table->unsignedBigInteger('feedback_id')->nullable();
            $table->foreign('feedback_id')->references('id')->on('feedbacks')->onDelete('set null');
        });
    }
};
