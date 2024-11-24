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
        Schema::create('role_actions', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('project_id'); // Foreign Key to projects table
            $table->unsignedBigInteger('user_id'); // Foreign Key to users table
            $table->enum('role', ['contributor', 'reviewer', 'approver', 'publisher']); // User role
            $table->string('action'); // Action (e.g., review, request change, reject, approve, publish)
            $table->string('status')->nullable(); // Project status at the time of action
            $table->text('comment')->nullable(); // Optional comment for the action
            $table->timestamps(); // Created and Updated timestamps

            // Foreign Key Constraints
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_actions');
    }
};
