<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'partial', 'received', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }

    /**
     * Update the migrations.
     */
    public function update()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'completed', 'cancelled'])->default('pending')->change();
        });
    }
};
