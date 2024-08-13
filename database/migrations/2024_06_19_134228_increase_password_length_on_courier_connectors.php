<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreasePasswordLengthOnCourierConnectors extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE courier_connectors MODIFY COLUMN password VARCHAR(1024)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE courier_connectors MODIFY COLUMN password VARCHAR(255)");
    }
}
