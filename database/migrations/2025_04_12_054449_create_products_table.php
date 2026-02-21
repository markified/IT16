<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable(); // Allow NULL values for description
            $table->string('type');
            $table->integer('quantity')->default(0);
            $table->decimal('price_per_item', 10, 2)->default(0); // Removed the `after` clause
            $table->integer('min_stock_level')->default(5);
            $table->string('serial_number')->nullable()->unique();
            $table->text('specifications')->nullable();
            $table->enum('status', ['available', 'assigned', 'maintenance', 'retired'])->default('available');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
