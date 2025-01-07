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
        Schema::create('terminal_report_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terminal_report_id')->constrained('terminal_reports')->onDelete('cascade');
            $table->binary('file'); // Assuming the file is stored as binary
            $table->string('original_filename')->nullable();
            $table->string('extension')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terminal_report_files');
    }
};
