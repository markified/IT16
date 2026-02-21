<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
            $table->foreign('purchase_order_id')
                ->references('id')->on('purchase_orders')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
            $table->foreign('purchase_order_id')
                ->references('id')->on('purchase_orders');
        });
    }
}; 