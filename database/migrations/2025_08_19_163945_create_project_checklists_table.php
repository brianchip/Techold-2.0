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
        Schema::create('project_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('checklist_name');
            $table->enum('checklist_type', [
                'Pre-Commissioning', 'Safety', 'Performance', 'Quality', 'Final Inspection', 'Other'
            ])->default('Other');
            $table->enum('status', ['Draft', 'Active', 'In Progress', 'Completed', 'Cancelled'])->default('Draft');
            $table->integer('total_items')->default(0);
            $table->integer('completed_items')->default(0);
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->date('due_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('employees')->onDelete('set null');

            // Indexes
            $table->index(['project_id', 'status']);
            $table->index(['checklist_type', 'status']);
        });

        // Create checklist items table
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_id');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->enum('status', ['Pending', 'In Progress', 'Completed', 'Failed', 'N/A'])->default('Pending');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->boolean('is_critical')->default(false);
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Test results, measurements, etc.
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('checklist_id')->references('id')->on('project_checklists')->onDelete('cascade');
            $table->foreign('completed_by')->references('id')->on('employees')->onDelete('set null');

            // Indexes
            $table->index(['checklist_id', 'status']);
            $table->index(['priority', 'is_critical']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
        Schema::dropIfExists('project_checklists');
    }
};