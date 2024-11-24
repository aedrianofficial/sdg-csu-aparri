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
        Schema::rename('sdg_report', 'report_sdg');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_report_sdg', function (Blueprint $table) {
            //
        });
    }
};
