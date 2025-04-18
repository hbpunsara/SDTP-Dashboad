<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirQualityReadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('air_quality_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')->constrained('sensors')->onDelete('cascade');
            $table->integer('aqi')->comment('Air Quality Index');
            $table->float('pm25', 8, 2)->comment('PM2.5 value in μg/m³');
            $table->float('pm10', 8, 2)->comment('PM10 value in μg/m³');
            $table->float('o3', 8, 2)->nullable()->comment('Ozone value in ppb');
            $table->float('no2', 8, 2)->nullable()->comment('Nitrogen dioxide value in ppb');
            $table->float('so2', 8, 2)->nullable()->comment('Sulfur dioxide value in ppb');
            $table->float('co', 8, 2)->nullable()->comment('Carbon monoxide value in ppm');
            $table->string('aqi_category')->comment('AQI category: Good, Moderate, Unhealthy, etc.');
            $table->timestamp('reading_time')->useCurrent();
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
        Schema::dropIfExists('air_quality_readings');
    }
}
