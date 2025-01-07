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
        Schema::create('sdg_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->string('sub_category_name')->unique();
            $table->text('sub_category_description')->nullable();
            $table->foreignId('sdg_id')->constrained('sdgs')->onDelete('cascade'); // Assuming there's an `sdgs` table.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sdg_sub_categories');
    }
};
