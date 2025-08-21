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
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('milestone_name');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->date('completion_date')->nullable();
            $table->enum('status', ['Planned', 'In Progress', 'Completed', 'Overdue', 'Cancelled'])->default('Planned');
            $table->integer('progress_percent')->default(0);
            $table->boolean('is_critical')->default(false);
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('employees')->onDelete('set null');

            // Indexes
            $table->index(['project_id', 'status']);
            $table->index(['due_date', 'status']);
            $table->index('is_critical');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_milestones');
    }
};