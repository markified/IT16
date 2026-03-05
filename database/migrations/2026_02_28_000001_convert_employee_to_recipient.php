<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, copy employee names to a new recipient column
        Schema::table('inventory_issues', function (Blueprint $table) {
            $table->string('recipient')->nullable()->after('product_id');
        });

        // Copy employee names to recipient
        DB::statement('
            UPDATE inventory_issues 
            SET recipient = (SELECT name FROM employees WHERE employees.id = inventory_issues.employee_id)
            WHERE employee_id IS NOT NULL
        ');

        // Remove the employee_id foreign key and column
        Schema::table('inventory_issues', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_issues', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->after('product_id')->constrained('employees');
            $table->dropColumn('recipient');
        });
    }
};
