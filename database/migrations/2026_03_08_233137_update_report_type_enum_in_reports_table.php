<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `reports` MODIFY COLUMN `report_type` ENUM('inventory', 'stock_out_order', 'supplier', 'department')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `reports` MODIFY COLUMN `report_type` ENUM('inventory', 'purchase_order', 'issue', 'supplier', 'department')");
    }
};
