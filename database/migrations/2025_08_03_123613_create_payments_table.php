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
        Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->decimal('gross_salary', 10, 2)->nullable();
        $table->decimal('per_day_rate', 10, 2)->nullable();
        $table->integer('present_days')->nullable();
        $table->decimal('ot_arrears', 10, 2)->nullable();
        $table->decimal('other_allowance', 10, 2)->nullable();
        $table->decimal('gross_payable', 10, 2)->nullable();
        $table->decimal('pf', 10, 2)->nullable();
        $table->decimal('insurance', 10, 2)->nullable();
        $table->decimal('pt', 10, 2)->nullable();
        $table->decimal('advance', 10, 2)->nullable();
        $table->decimal('total_deduction', 10, 2)->nullable();
        $table->decimal('net_payable', 10, 2)->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
