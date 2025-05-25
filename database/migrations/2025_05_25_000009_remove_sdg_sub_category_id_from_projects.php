<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'sdg_sub_category_id')) {
                $table->dropColumn('sdg_sub_category_id');
            }
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'sdg_sub_category_id')) {
                $table->json('sdg_sub_category_id')->nullable();
            }
        });
    }
};
