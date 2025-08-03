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
        Schema::create('emppayments', function (Blueprint $table) {
               $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->date('from_date');
        $table->date('to_date');
        $table->integer('present_days')->default(0);
        $table->decimal('gross_salary', 10, 2)->default(0);
        $table->decimal('per_day_rate', 10, 2)->default(0);
        $table->decimal('basic_60', 10, 2)->default(0);
        $table->decimal('hra_5', 10, 2)->default(0);
        $table->decimal('conveyance_20', 10, 2)->default(0);
        $table->decimal('other_allowance', 10, 2)->default(0);
        $table->decimal('ot_arrears', 10, 2)->default(0);
        $table->decimal('gross_payable', 10, 2)->default(0);
        $table->decimal('pf_12', 10, 2)->default(0);
        $table->decimal('insurance', 10, 2)->default(0);
        $table->decimal('pt', 10, 2)->default(0);
        $table->decimal('advance', 10, 2)->default(0);
        $table->decimal('total_deduction', 10, 2)->default(0);
        $table->decimal('net_payable', 10, 2)->default(0);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emppayments');
    }
};
