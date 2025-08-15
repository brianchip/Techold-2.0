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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('parent_task_id')->nullable()->comment('For WBS hierarchy');
            $table->string('task_name');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('planned_cost', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->integer('progress_percent')->default(0);
            $table->enum('dependency_type', ['FS', 'SS', 'FF', 'SF'])->nullable()->comment('Finish-Start, Start-Start, Finish-Finish, Start-Finish');
            $table->unsignedBigInteger('dependency_task_id')->nullable()->comment('Task this depends on');
            $table->enum('status', ['Not Started', 'In Progress', 'Completed', 'On Hold', 'Cancelled'])->default('Not Started');
            $table->integer('priority')->default(3)->comment('1=High, 2=Medium, 3=Low');
            $table->integer('estimated_hours')->nullable();
            $table->integer('actual_hours')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('parent_task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('dependency_task_id')->references('id')->on('tasks')->onDelete('set null');

            // Indexes for performance
            $table->index(['project_id', 'status']);
            $table->index(['parent_task_id', 'status']);
            $table->index(['dependency_task_id']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
