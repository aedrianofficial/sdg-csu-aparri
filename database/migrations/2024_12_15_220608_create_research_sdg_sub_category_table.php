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
        Schema::create('research_sdg_sub_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('research_id');
            $table->unsignedBigInteger('sdg_sub_category_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('research_id')->references('id')->on('research')->onDelete('cascade');
            $table->foreign('sdg_sub_category_id')->references('id')->on('sdg_sub_categories')->onDelete('cascade');

            // Ensure no duplicate entries
            $table->unique(['research_id', 'sdg_sub_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_sdg_sub_category');
    }
};
