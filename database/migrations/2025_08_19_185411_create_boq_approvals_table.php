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
        Schema::create('boq_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('boq_version_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('approval_type', ['Engineering Manager', 'Finance Manager', 'Managing Director', 'Client'])->default('Engineering Manager');
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Revision Required'])->default('Pending');
            $table->foreignId('approver_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->text('comments')->nullable();
            $table->json('approval_data')->nullable(); // Additional data like revised amounts, conditions
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->integer('approval_order')->default(1); // Sequential approval order
            $table->boolean('is_required')->default(true);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->json('approval_conditions')->nullable(); // Any conditions attached to approval
            $table->timestamps();

            $table->index(['project_id', 'approval_type']);
            $table->index(['project_id', 'status']);
            $table->index(['approver_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boq_approvals');
    }
};