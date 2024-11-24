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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->text('feedback');
            $table->unsignedBigInteger('users_id'); // Use users_id instead of reviewer_id and approver_id
            $table->timestamps();

            // Add foreign key constraint
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropForeign(['users_id']);
        });

        Schema::dropIfExists('feedbacks');
    }
};
