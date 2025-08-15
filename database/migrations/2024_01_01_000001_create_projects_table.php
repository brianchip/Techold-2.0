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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code')->unique()->comment('Auto-generated project code');
            $table->string('project_name');
            $table->enum('project_type', ['Engineering', 'Procurement', 'Installation', 'EPC']);
            $table->unsignedBigInteger('client_id')->comment('FK from CRM module');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['Planned', 'In Progress', 'Completed', 'On Hold', 'Cancelled'])->default('Planned');
            $table->unsignedBigInteger('project_manager_id')->comment('FK from HR module');
            $table->text('description')->nullable();
            $table->decimal('total_budget', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->integer('progress_percent')->default(0);
            $table->string('location')->nullable();
            $table->json('metadata')->nullable()->comment('Additional project-specific data');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['status', 'start_date']);
            $table->index(['client_id', 'status']);
            $table->index(['project_manager_id', 'status']);
            $table->index('project_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
