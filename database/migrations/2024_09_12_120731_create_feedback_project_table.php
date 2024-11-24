<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
    {
        Schema::create('feedback_project', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('feedback_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('feedback_id')->references('id')->on('feedbacks')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('feedback_project');
    }
};
