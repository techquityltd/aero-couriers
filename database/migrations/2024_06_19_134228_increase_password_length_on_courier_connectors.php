<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreasePasswordLengthForCourierConnectorsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courier_connectors', function (Blueprint $table) {
            $table->string('password', 1024)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courier_connectors', function (Blueprint $table) {
            $table->string('password', 255)->change();
        });
    }
}
