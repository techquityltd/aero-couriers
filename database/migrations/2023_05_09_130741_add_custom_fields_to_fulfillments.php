<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomFieldsToFulfillments extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fulfillments', function (Blueprint $table) {
            $table->string('custom_fields')->after('delivery_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fulfillments', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });
    }
};
