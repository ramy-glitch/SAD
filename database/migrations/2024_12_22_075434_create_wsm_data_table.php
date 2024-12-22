<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWsmDataTable extends Migration
{
    public function up()
    {
        Schema::create('wsm_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->json('criteria_data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wsm_data');
    }
}