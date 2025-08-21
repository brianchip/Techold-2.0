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
            // Add indexes for frequently searched/filtered columns (only if not exists)
            $table->index(['status', 'created_at'], 'projects_status_created_idx');
            $table->index(['end_date', 'status'], 'projects_end_date_status_idx');
            $table->index(['progress_percent'], 'projects_progress_idx');
            $table->index(['project_type', 'status'], 'projects_type_status_idx');
            
            // Composite index for common queries
            $table->index(['status', 'end_date', 'created_at'], 'projects_status_end_created_idx');
        });

        // Add indexes to related tables for better join performance
        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['project_id', 'status'], 'tasks_project_status_idx');
            $table->index(['project_id', 'progress_percent'], 'tasks_project_progress_idx');
        });

        Schema::table('budget_lines', function (Blueprint $table) {
            $table->index(['project_id', 'status'], 'budget_lines_project_status_idx');
            $table->index(['project_id', 'category'], 'budget_lines_project_category_idx');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->index(['project_id', 'category'], 'documents_project_category_idx');
            $table->index(['project_id', 'uploaded_at'], 'documents_project_uploaded_idx');
        });

        Schema::table('risks', function (Blueprint $table) {
            $table->index(['project_id', 'status'], 'risks_project_status_idx');
            $table->index(['project_id', 'severity'], 'risks_project_severity_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_status_created_idx');
            $table->dropIndex('projects_end_date_status_idx');
            $table->dropIndex('projects_progress_idx');
            $table->dropIndex('projects_type_status_idx');
            $table->dropIndex('projects_status_end_created_idx');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_project_status_idx');
            $table->dropIndex('tasks_project_progress_idx');
        });

        Schema::table('budget_lines', function (Blueprint $table) {
            $table->dropIndex('budget_lines_project_status_idx');
            $table->dropIndex('budget_lines_project_category_idx');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('documents_project_category_idx');
            $table->dropIndex('documents_project_uploaded_idx');
        });

        Schema::table('risks', function (Blueprint $table) {
            $table->dropIndex('risks_project_status_idx');
            $table->dropIndex('risks_project_severity_idx');
        });
    }
};