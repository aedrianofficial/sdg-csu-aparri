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
        Schema::table('role_actions', function (Blueprint $table) {
            // Drop the project_id column
            if (Schema::hasColumn('role_actions', 'project_id')) {
                $table->dropForeign(['project_id']);
                $table->dropColumn('project_id');
                $table->dropColumn('status');
                $table->dropColumn('comment');
            }

            // Add polymorphic columns for content reference
            $table->unsignedBigInteger('content_id')->nullable()->after('user_id'); // ID of the related content
            $table->string('content_type')->nullable()->after('content_id'); // Type of the related content

            // Add an index for the polymorphic fields
            $table->index(['content_id', 'content_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_actions', function (Blueprint $table) {
            // Drop the polymorphic columns and index
            $table->dropIndex(['content_id', 'content_type']);
            $table->dropColumn(['content_id', 'content_type']);

            // Restore the project_id column
            $table->unsignedBigInteger('project_id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }
};
