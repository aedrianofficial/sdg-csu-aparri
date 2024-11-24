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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->before('password');
            $table->string('address')->nullable()->before('phone_number');
            $table->date('date_of_birth')->nullable()->before('address');
            $table->text('bio')->nullable()->before('date_of_birth');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'address', 'city', 'state', 'zip_code', 'country', 'date_of_birth', 'bio']);
        });
    }
};
