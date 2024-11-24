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
        Schema::create('research_researchfile', function (Blueprint $table) {
            $table->unsignedBigInteger('research_id');
            $table->unsignedBigInteger('researchfile_id');
            $table->foreign('research_id')->references('id')->on('research')->onDelete('cascade');
            $table->foreign('researchfile_id')->references('id')->on('researchfiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_researchfile');
    }
};
