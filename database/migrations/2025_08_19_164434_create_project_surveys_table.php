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
        Schema::create('project_surveys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('survey_name');
            $table->enum('survey_type', [
                'Site Survey', 'Technical Assessment', 'Safety Inspection', 'Progress Review', 'Final Inspection', 'Other'
            ])->default('Site Survey');
            $table->string('location')->nullable();
            $table->date('survey_date');
            $table->enum('status', ['Scheduled', 'In Progress', 'Completed', 'Cancelled'])->default('Scheduled');
            $table->integer('photos_count')->default(0);
            $table->integer('gps_points_count')->default(0);
            $table->unsignedBigInteger('surveyor_id')->nullable();
            $table->text('notes')->nullable();
            $table->json('survey_data')->nullable(); // Form responses, measurements, etc.
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('surveyor_id')->references('id')->on('employees')->onDelete('set null');

            // Indexes
            $table->index(['project_id', 'status']);
            $table->index(['survey_date', 'status']);
            $table->index('survey_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_surveys');
    }
};