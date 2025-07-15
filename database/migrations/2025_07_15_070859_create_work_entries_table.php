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
        Schema::create('work_entries', function (Blueprint $table) {
            $table->id();

            // Date of entry
            $table->date('date');

            // Foreign keys for chapter and supervisor - assuming you have chapters and supervisors tables
            $table->foreignId('chapter_id')->constrained()->onDelete('cascade');
            $table->foreignId('supervisor_id')->constrained()->onDelete('cascade');

            // Description and unit
            $table->string('description'); // Can be text or string depending on your data
            $table->string('unit');

            // Measurements
            $table->decimal('length', 10, 2)->default(0);
            $table->decimal('breadth', 10, 2)->default(0);
            $table->decimal('height', 10, 2)->default(0);

            // Total quantity (calculated)
            $table->decimal('total_quantity', 14, 4)->default(0);

            // Labour counts stored as JSON (to store counts per trade)
            $table->json('labour')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_entries');
    }
};
