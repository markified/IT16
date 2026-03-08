<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // e.g., CPU, GPU, RAM
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // FontAwesome icon class
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add category_id and SKU to products table
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('sku')->nullable()->unique()->after('name');
            $table->string('barcode')->nullable()->unique()->after('sku');
            $table->string('brand')->nullable()->after('barcode');
            $table->string('model_number')->nullable()->after('brand');
            $table->string('location')->nullable()->after('specifications'); // Storage location
            $table->decimal('cost_price', 10, 2)->default(0)->after('price_per_item'); // Purchase cost
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'sku', 'barcode', 'brand', 'model_number', 'location', 'cost_price']);
        });

        Schema::dropIfExists('categories');
    }
};
