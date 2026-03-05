<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('reference_number')->unique();
            $table->enum('adjustment_type', ['increase', 'decrease', 'correction']);
            $table->integer('quantity_before');
            $table->integer('quantity_adjusted'); // Can be positive or negative
            $table->integer('quantity_after');
            $table->enum('reason', [
                'damaged',
                'expired',
                'lost',
                'theft',
                'found',
                'counting_error',
                'return',
                'other'
            ]);
            $table->text('notes')->nullable();
            $table->foreignId('adjusted_by')->constrained('users');
            $table->date('adjustment_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
