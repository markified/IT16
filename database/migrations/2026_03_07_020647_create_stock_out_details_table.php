<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_out_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_out_order_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->integer('quantity_issued');
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('stock_out_order_id')->references('id')->on('stock_out_orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_out_details');
    }
};
