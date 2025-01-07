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
        Schema::create('status_reports', function (Blueprint $table) {
            $table->id();
            $table->string('related_type'); // e.g., Project, Research
            $table->unsignedBigInteger('related_id');
            $table->string('related_title');
            $table->string('log_status'); // Status update log
            $table->text('remarks')->nullable();
            $table->string('files_link')->nullable();
            $table->unsignedBigInteger('logged_by_id'); // ID of the user who logged this report
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_reports');
    }
};
