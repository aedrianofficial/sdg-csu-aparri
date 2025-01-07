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
        Schema::create('terminal_report_researcher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terminal_report_id')->constrained()->onDelete('cascade'); // References terminal_reports table
            $table->foreignId('researcher_id')->constrained()->onDelete('cascade'); // References researchers table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terminal_report_researcher');
    }
};
