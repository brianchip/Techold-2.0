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
        Schema::create('boq_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('boq_section_id');
            $table->unsignedBigInteger('project_id');
            $table->string('item_code');
            $table->text('description');
            $table->string('unit', 50); // Each, Meter, Square Meter, etc.
            $table->decimal('quantity', 12, 3);
            $table->decimal('rate', 12, 2);
            $table->decimal('total_amount', 15, 2);
            $table->enum('category', [
                'Materials', 'Labor', 'Equipment', 'Subcontractor', 'Overhead', 'Other'
            ])->default('Materials');
            $table->enum('status', ['Draft', 'Approved', 'Revised', 'Cancelled'])->default('Draft');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional item properties
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('boq_section_id')->references('id')->on('boq_sections')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            // Indexes
            $table->index(['boq_section_id', 'status']);
            $table->index(['project_id', 'category']);
            $table->index('item_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boq_items');
    }
};