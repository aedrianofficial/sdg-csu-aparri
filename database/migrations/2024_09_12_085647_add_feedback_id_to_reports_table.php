<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('feedback_id')->nullable()->after('user_id'); // Add after an existing column
            $table->foreign('feedback_id')->references('id')->on('feedbacks')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['feedback_id']);
            $table->dropColumn('feedback_id');
        });
    }
};
