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
        Schema::create('boq_library_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('item_name');
            $table->text('description');
            $table->enum('category', ['Materials', 'Labor', 'Equipment', 'Subcontractor', 'Overhead', 'Other'])->default('Materials');
            $table->string('unit', 50);
            $table->decimal('standard_rate', 15, 2);
            $table->decimal('min_rate', 15, 2)->nullable();
            $table->decimal('max_rate', 15, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->text('specifications')->nullable();
            $table->json('custom_fields')->nullable(); // Additional flexible fields
            $table->boolean('is_active')->default(true);
            $table->boolean('is_template')->default(false); // Can be used as template
            $table->integer('usage_count')->default(0); // Track how often used
            $table->date('last_updated_price')->nullable();
            $table->foreignId('created_by')->constrained('employees')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->index(['item_code', 'is_active']);
            $table->index('usage_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boq_library_items');
    }
};