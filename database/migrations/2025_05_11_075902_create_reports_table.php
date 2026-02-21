<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('serial_number')->nullable();
            $table->text('description')->nullable();
            $table->text('specifications')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('price_per_item', 10, 2)->default(0);
            $table->enum('status', ['available', 'assigned', 'maintenance', 'retired'])->default('available');
            $table->enum('report_type', ['inventory', 'purchase_order', 'issue', 'supplier', 'department']);
            $table->json('parameters')->nullable(); // Store report parameters
            $table->json('data')->nullable(); // Store generated report data
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('report_date');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
