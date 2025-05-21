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
        Schema::create('gender_impacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('research_id');
            $table->boolean('benefits_men')->default(false);
            $table->boolean('benefits_women')->default(false);
            $table->boolean('benefits_all')->default(false);
            $table->boolean('addresses_gender_inequality')->default(false);
            $table->integer('men_count')->nullable();
            $table->integer('women_count')->nullable();
            $table->text('gender_notes')->nullable();
            $table->timestamps();
            
            $table->foreign('research_id')
                ->references('id')
                ->on('research')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gender_impacts');
    }
}; 