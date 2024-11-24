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
        Schema::create('research_sdg', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sdg_id');
            $table->foreign('sdg_id')->references('id')->on('sdgs')->onDelete('cascade');
            $table->unsignedBigInteger('research_id');
            $table->foreign('research_id')->references('id')->on('researches')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research_table', function (Blueprint $table) {
            //
        });
    }
};
