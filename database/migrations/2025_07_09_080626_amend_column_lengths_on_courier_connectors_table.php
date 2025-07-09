<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AmendColumnLengthsOnCourierConnectorsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $columns = [
            'name',
            'carrier',
            'url',
            'user',
            'token'
        ];

        foreach ($columns as $column) {
            DB::statement("ALTER TABLE courier_connectors MODIFY COLUMN $column VARCHAR(1024)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = [
            'name',
            'carrier',
            'url',
            'user',
            'token'
        ];

        foreach ($columns as $column) {
            DB::statement("ALTER TABLE courier_connectors MODIFY COLUMN $column VARCHAR(255)");
        }
    }
};
