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
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('task_id')->nullable();
            $table->string('risk_title');
            $table->text('description');
            $table->enum('severity', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->enum('probability', ['Very Low', 'Low', 'Medium', 'High', 'Very High'])->default('Medium');
            $table->enum('impact', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->decimal('risk_score', 5, 2)->default(0)->comment('Severity * Probability * Impact');
            $table->text('mitigation_plan')->nullable();
            $table->text('contingency_plan')->nullable();
            $table->enum('status', ['Identified', 'Assessed', 'Mitigated', 'Monitored', 'Closed'])->default('Identified');
            $table->unsignedBigInteger('assigned_to')->nullable()->comment('FK from HR module');
            $table->date('target_mitigation_date')->nullable();
            $table->date('actual_mitigation_date')->nullable();
            $table->decimal('mitigation_cost', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('employees')->onDelete('set null');

            // Indexes for performance
            $table->index(['project_id', 'status']);
            $table->index(['task_id', 'status']);
            $table->index(['severity', 'probability']);
            $table->index(['assigned_to', 'status']);
            $table->index('risk_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};
