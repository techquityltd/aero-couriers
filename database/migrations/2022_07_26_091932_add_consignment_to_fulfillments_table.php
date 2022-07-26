<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConsignmentToFulfillmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fulfillments', function (Blueprint $table) {
            $table->unsignedInteger('parent_id')->after('state')->nullable();

            $table->foreign('parent_id')->references('id')->on('fulfillments')->onDelete('cascade');
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
            $table->dropForeign('fulfillments_parent_id_foreign');
            $table->dropColumn('parent_id');
        });
    }
}
