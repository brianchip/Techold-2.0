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
        Schema::create('boq_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('version_number'); // e.g., "1.0", "1.1", "2.0"
            $table->string('version_name')->nullable(); // e.g., "Initial Version", "Revised Electrical"
            $table->text('description')->nullable();
            $table->enum('status', ['Draft', 'Active', 'Archived', 'Approved'])->default('Draft');
            $table->boolean('is_current')->default(false);
            $table->json('snapshot_data'); // JSON snapshot of BOQ sections and items
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->integer('sections_count')->default(0);
            $table->integer('items_count')->default(0);
            $table->foreignId('created_by')->constrained('employees')->onDelete('cascade');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamps();

            $table->index(['project_id', 'version_number']);
            $table->index(['project_id', 'is_current']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boq_versions');
    }
};