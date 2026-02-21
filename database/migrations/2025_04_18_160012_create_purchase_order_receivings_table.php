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
        Schema::create('purchase_order_receivings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_detail_id')->constrained();
            $table->date('received_date');
            $table->integer('quantity_received');
            $table->string('received_by');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_order_receivings');
    }
};
