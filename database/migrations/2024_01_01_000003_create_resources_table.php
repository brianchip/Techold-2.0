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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('employee_id')->nullable()->comment('FK from HR module');
            $table->unsignedBigInteger('equipment_id')->nullable()->comment('FK from Assets module');
            $table->string('role')->nullable()->comment('Role for human resources');
            $table->integer('allocated_hours')->default(0);
            $table->integer('actual_hours')->default(0);
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->date('allocation_start_date');
            $table->date('allocation_end_date');
            $table->enum('status', ['Allocated', 'Active', 'Completed', 'Cancelled'])->default('Allocated');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('set null');

            // Indexes for performance
            $table->index(['task_id', 'status']);
            $table->index(['employee_id', 'status']);
            $table->index(['equipment_id', 'status']);
            $table->index(['allocation_start_date', 'allocation_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
