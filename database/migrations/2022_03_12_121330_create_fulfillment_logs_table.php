<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFulfillmentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fulfillment_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('fulfillment_id');
            $table->string('title');
            $table->text('message');
            $table->string('type')->nullable();
            $table->json('data')->nullable()
            ;

            $table->timestamps();

            $table->foreign('fulfillment_id')->references('id')->on('fulfillments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fulfillment_logs');
    }
}
