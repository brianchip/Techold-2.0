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
        Schema::create('project_quotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('supplier_name');
            $table->string('supplier_contact')->nullable();
            $table->string('quote_reference')->nullable();
            $table->decimal('quote_amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->date('quote_date');
            $table->date('valid_until')->nullable();
            $table->text('items_description');
            $table->json('line_items')->nullable()->comment('Detailed quote breakdown');
            $table->enum('status', ['Pending', 'Selected', 'Rejected', 'Expired'])->default('Pending');
            $table->boolean('is_authorized_distributor')->default(false);
            $table->boolean('is_emergency_quote')->default(false);
            $table->text('notes')->nullable();
            $table->string('quote_file_path')->nullable()->comment('Uploaded quote document');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            
            // Indexes
            $table->index(['project_id', 'status']);
            $table->index(['supplier_name', 'project_id']);
            $table->index(['quote_date', 'valid_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_quotes');
    }
};