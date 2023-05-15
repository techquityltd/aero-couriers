<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courier_services', function (Blueprint $table) {
            $table->unsignedBigInteger('courier_service_group_id')->nullable()->after('description');

            $table->foreign('courier_service_group_id')
                ->references('id')
                ->on('courier_service_groups')
                ->onDelete('set null');

            $table->dropColumn('group'); // We don't need the string version anymore
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courier_services', function (Blueprint $table) {
            $table->dropColumn('courier_service_group_id');
            $table->string('group')->nullable()->after('description');
        });
    }
};
