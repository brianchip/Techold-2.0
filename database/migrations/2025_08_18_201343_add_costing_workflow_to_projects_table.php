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
        Schema::table('projects', function (Blueprint $table) {
            // Costing workflow fields
            $table->enum('costing_type', ['Tender/Proposal', 'Merchandise', 'Service Sales'])->default('Tender/Proposal')->after('project_type');
            $table->unsignedBigInteger('prime_mover_id')->nullable()->after('project_manager_id')->comment('Engineer responsible for tender costing');
            
            // Approval workflow
            $table->boolean('engineering_manager_approved')->default(false);
            $table->timestamp('engineering_manager_approved_at')->nullable();
            $table->unsignedBigInteger('engineering_manager_id')->nullable();
            
            $table->boolean('finance_manager_approved')->default(false);
            $table->timestamp('finance_manager_approved_at')->nullable();
            $table->unsignedBigInteger('finance_manager_id')->nullable();
            
            $table->boolean('md_approved')->default(false);
            $table->timestamp('md_approved_at')->nullable();
            $table->unsignedBigInteger('md_id')->nullable();
            
            // SAP integration fields
            $table->string('sap_project_code')->nullable()->comment('SAP system project code');
            $table->decimal('procurement_budget', 15, 2)->default(0);
            $table->decimal('actual_procurement_cost', 15, 2)->default(0);
            
            // Variance tracking
            $table->decimal('budget_variance', 15, 2)->default(0)->comment('Actual - Budget');
            $table->decimal('budget_variance_percent', 5, 2)->default(0);
            
            // Project closeout
            $table->boolean('is_closed_out')->default(false);
            $table->timestamp('closed_out_at')->nullable();
            $table->text('closeout_notes')->nullable();
            
            // Emergency procurement flag
            $table->boolean('emergency_procurement')->default(false);
            $table->text('emergency_justification')->nullable();
            
            // Add indexes
            $table->index(['costing_type', 'status']);
            $table->index(['engineering_manager_approved', 'finance_manager_approved', 'md_approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'costing_type',
                'prime_mover_id',
                'engineering_manager_approved',
                'engineering_manager_approved_at',
                'engineering_manager_id',
                'finance_manager_approved',
                'finance_manager_approved_at',
                'finance_manager_id',
                'md_approved',
                'md_approved_at',
                'md_id',
                'sap_project_code',
                'procurement_budget',
                'actual_procurement_cost',
                'budget_variance',
                'budget_variance_percent',
                'is_closed_out',
                'closed_out_at',
                'closeout_notes',
                'emergency_procurement',
                'emergency_justification'
            ]);
        });
    }
};