<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateBoxesTable extends Migration
{

    use DatabaseMigrations;
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boxes', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->string('serial_id')->nullable();
            $table->string('lat_long')->nullable();
            $table->string('ssid')->nullable();
            $table->string('password')->nullable();
            $table->string('bluetooth_name')->nullable();
            $table->integer('connected')->default(0);
            $table->integer('get_connectivity')->default(0);
            $table->integer('get_values')->default(0);
            $table->string('normal_compartment_temp')->nullable();
            $table->string('normal_compartment_humidity')->nullable();
            $table->string('hot_compartment_temp')->nullable();
            $table->string('hot_compartment_humidity')->nullable();
            $table->string('cold_compartment_temp')->nullable();
            $table->string('cold_compartment_humidity')->nullable();
            $table->integer('hot_compartment_actual_height')->nullable();
            $table->integer('hot_compartment_available_height')->nullable();
            $table->integer('cold_compartment_actual_height')->nullable();
            $table->integer('cold_compartment_available_height')->nullable();
            $table->integer('boxable_id')->unsigned();
            $table->integer('is_active')->default(0);
            $table->integer('reset_to_hotspot')->default(0);
            $table->integer('charging')->default(0);
            $table->string('boxable_type')->nullable();
            $table->string('image_url')->default('http://developers.exdnow.com:8080/brainbox-customer-portal/images/brainbox_logo_black.png');
            $table->string('mobile_image_url')->nullable();
            $table->string('activation_code')->nullable();
            $table->bigInteger('cloud_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('owned_by')->nullable();
            $table->integer('last_updated_by')->nullable();
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
        Schema::dropIfExists('boxes');
    }
}
