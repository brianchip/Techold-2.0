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
        Schema::create('project_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->enum('approval_type', [
                'Tender Sign Off',
                'Merchandise Costing',
                'Service Sales Costing',
                'Budget Variance',
                'Project Closeout'
            ]);
            $table->enum('approver_role', [
                'Prime Mover',
                'Engineering Manager',
                'Finance Manager',
                'Managing Director',
                'Accountant'
            ]);
            $table->unsignedBigInteger('approver_id');
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Requires Revision'])->default('Pending');
            $table->text('comments')->nullable();
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('responded_at')->nullable();
            $table->json('approval_data')->nullable()->comment('Additional approval context');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('approver_id')->references('id')->on('employees');
            
            // Indexes
            $table->index(['project_id', 'approval_type', 'status']);
            $table->index(['approver_id', 'status']);
            $table->index(['submitted_at', 'responded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_approvals');
    }
};