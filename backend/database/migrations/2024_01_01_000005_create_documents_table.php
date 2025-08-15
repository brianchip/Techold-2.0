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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('task_id')->nullable();
            $table->string('file_name');
            $table->string('original_file_name');
            $table->string('file_path');
            $table->string('file_url')->nullable();
            $table->string('file_type');
            $table->bigInteger('file_size')->comment('Size in bytes');
            $table->enum('category', [
                'Contracts & BOQs',
                'Design & Drawings',
                'Site Surveys',
                'Procurement & Invoices',
                'Progress Reports',
                'SHEQ',
                'Photos & Media',
                'Meeting Minutes',
                'Other'
            ]);
            $table->string('version')->default('1.0');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('uploaded_by')->comment('FK from HR module');
            $table->timestamp('uploaded_at');
            $table->json('metadata')->nullable()->comment('Additional file metadata');
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('set null');
            $table->foreign('uploaded_by')->references('id')->on('employees')->onDelete('cascade');

            // Indexes for performance
            $table->index(['project_id', 'category']);
            $table->index(['task_id', 'category']);
            $table->index(['uploaded_by', 'uploaded_at']);
            $table->index('file_type');
            $table->index('version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
