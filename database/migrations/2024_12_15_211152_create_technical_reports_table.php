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
        Schema::create('terminal_reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('related_type'); // e.g., Project, Research
            $table->unsignedBigInteger('related_id');
            $table->unsignedBigInteger('researchers_id');
            $table->unsignedBigInteger('cooperating_agency_id')->nullable();
            $table->unsignedBigInteger('funding_agency_id')->nullable();
            $table->date('date_started');
            $table->date('date_ended')->nullable();
            $table->decimal('total_approved_budget', 15, 2);
            $table->decimal('actual_released_budget', 15, 2)->nullable();
            $table->decimal('actual_expenditure', 15, 2)->nullable();
            $table->text('abstract')->nullable();
            $table->string('files_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terminal_reports');
    }
};
