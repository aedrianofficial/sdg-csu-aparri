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
        Schema::create('feedback_report', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->unsignedBigInteger('feedback_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
            $table->foreign('feedback_id')->references('id')->on('feedbacks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('feedback_report');
    }
};
