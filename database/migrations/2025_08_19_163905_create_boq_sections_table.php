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
        Schema::create('boq_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('section_name');
            $table->string('section_code')->unique();
            $table->text('description')->nullable();
            $table->integer('display_order')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', ['Draft', 'Active', 'Completed', 'Archived'])->default('Draft');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            // Indexes
            $table->index(['project_id', 'status']);
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boq_sections');
    }
};