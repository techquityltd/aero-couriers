<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourierShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courier_shipments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('admin_id')->nullable();
            $table->unsignedBigInteger('courier_connector_id')->nullable();
            $table->unsignedBigInteger('courier_service_id')->nullable();
            $table->unsignedBigInteger('courier_collection_id')->nullable();
            $table->unsignedBigInteger('courier_printer_id')->nullable();
            $table->string('consignment_number')->nullable();
            $table->boolean('committed')->default(false);
            $table->boolean('failed')->default(false);
            $table->boolean('cancelled')->default(false);
            $table->string('label')->nullable();
            $table->longText('failed_messages')->nullable();
            $table->longText('request')->nullable();
            $table->longText('response')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('admin_id')
                ->references('id')
                ->on('admins')
                ->onDelete('set null');

            $table->foreign('courier_service_id')
                ->references('id')
                ->on('courier_services')
                ->onDelete('set null');

            $table->foreign('courier_connector_id')
                ->references('id')
                ->on('courier_connectors')
                ->onDelete('set null');

            $table->foreign('courier_collection_id')
                ->references('id')
                ->on('courier_collections')
                ->onDelete('set null');

            $table->foreign('courier_printer_id')
                ->references('id')
                ->on('courier_printers')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courier_shipments');
    }
}
