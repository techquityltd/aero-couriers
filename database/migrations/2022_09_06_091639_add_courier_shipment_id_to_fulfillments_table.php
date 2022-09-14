<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourierShipmentIdToFulfillmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fulfillments', function (Blueprint $table) {
            $table->unsignedBigInteger('courier_shipment_id')->after('state')->nullable();

            $table->foreign('courier_shipment_id')
                ->references('id')
                ->on('courier_shipments')
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
        Schema::table('fulfillments', function (Blueprint $table) {
            $table->dropForeign('fulfillments_courier_shipment_id_foreign');
            $table->dropColumn('courier_shipment_id');
        });
    }
}
