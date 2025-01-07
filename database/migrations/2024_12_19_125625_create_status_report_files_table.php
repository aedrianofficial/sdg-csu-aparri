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
        Schema::create('status_report_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_report_id')->constrained('status_reports')->onDelete('cascade');
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
        Schema::dropIfExists('status_report_files');
    }
};
