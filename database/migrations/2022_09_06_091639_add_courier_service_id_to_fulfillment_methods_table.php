<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourierServiceIdToFulfillmentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fulfillment_methods', function (Blueprint $table) {
            $table->unsignedBigInteger('courier_service_id')->after('driver')->nullable();
            $table->unsignedBigInteger('courier_connector_id')->after('driver')->nullable();
            $table->unsignedBigInteger('courier_printer_id')->after('driver')->nullable();

            $table->foreign('courier_service_id')
                ->references('id')
                ->on('courier_services')
                ->onDelete('set null');

            $table->foreign('courier_connector_id')
                ->references('id')
                ->on('courier_connectors')
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
        Schema::table('fulfillment_methods', function (Blueprint $table) {
            $table->dropForeign('fulfillment_methods_courier_service_id_foreign');
            $table->dropForeign('fulfillment_methods_courier_connector_id_foreign');
            $table->dropForeign('fulfillment_methods_courier_printer_id_foreign');

            $table->dropColumn([
                'courier_service_id',
                'courier_connector_id',
                'courier_printer_id',
            ]);
        });
    }
}
