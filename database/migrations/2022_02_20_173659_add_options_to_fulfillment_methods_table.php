<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptionsToFulfillmentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fulfillment_methods', function (Blueprint $table) {
            $table->string('courier')->after('driver')->nullable();
            $table->json('courier_configuration')->after('sort')->nullable();
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
            $table->dropColumn(['courier', 'courier_configuration']);
        });
    }
}
