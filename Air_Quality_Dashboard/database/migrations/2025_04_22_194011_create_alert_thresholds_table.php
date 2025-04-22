<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertThresholdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alert_thresholds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('level_name');
            $table->integer('min_value');
            $table->integer('max_value');
            $table->string('color');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('send_notification')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alert_thresholds');
    }
}
