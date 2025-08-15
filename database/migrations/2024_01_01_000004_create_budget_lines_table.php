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
        Schema::create('budget_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('task_id')->nullable();
            $table->enum('category', ['Material', 'Labor', 'Overhead', 'Equipment', 'Subcontractor', 'Other']);
            $table->string('description');
            $table->string('unit')->nullable();
            $table->decimal('quantity', 15, 4)->default(1);
            $table->decimal('unit_cost', 15, 4)->default(0);
            $table->decimal('planned_amount', 15, 2)->default(0);
            $table->decimal('actual_amount', 15, 2)->default(0);
            $table->decimal('variance', 15, 2)->default(0);
            $table->decimal('variance_percent', 5, 2)->default(0);
            $table->enum('status', ['Planned', 'Approved', 'In Progress', 'Completed', 'Cancelled'])->default('Planned');
            $table->string('boq_reference')->nullable()->comment('Bill of Quantities reference');
            $table->string('supplier_reference')->nullable()->comment('Supplier invoice reference');
            $table->date('planned_date')->nullable();
            $table->date('actual_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('set null');

            // Indexes for performance
            $table->index(['project_id', 'category']);
            $table->index(['task_id', 'category']);
            $table->index(['status', 'planned_date']);
            $table->index('boq_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_lines');
    }
};
