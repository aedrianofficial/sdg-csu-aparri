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
        Schema::create('feedback_research', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('research_id');
            $table->unsignedBigInteger('feedback_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('research_id')->references('id')->on('research')->onDelete('cascade');
            $table->foreign('feedback_id')->references('id')->on('feedbacks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('feedback_research');
    }
};
