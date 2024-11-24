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
        Schema::create('researchfiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('research_id');
            $table->foreign('research_id')->references('id')->on('researches')->onDelete('cascade');
            $table->string('file');
            $table->boolean('is_publish')->default(false);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('researchfiles');
    }
};
