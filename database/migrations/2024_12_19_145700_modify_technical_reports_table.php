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
        Schema::table('terminal_reports', function (Blueprint $table) {
            //
            Schema::table('terminal_reports', function (Blueprint $table) {
                $table->renameColumn('title', 'related_title');
                $table->renameColumn('files_link', 'related_link');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terminal_reports', function (Blueprint $table) {
            //
            Schema::table('terminal_reports', function (Blueprint $table) {
                $table->renameColumn('related_title', 'title');
                $table->renameColumn('related_link', 'files_link');
            });
            
        });
    }
};
