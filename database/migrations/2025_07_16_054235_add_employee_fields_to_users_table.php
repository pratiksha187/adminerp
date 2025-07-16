<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::table('users', function (Blueprint $table) {
    //         //
    //     });
    // }
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // $table->string('employee_code');
        $table->string('gender')->nullable();
        $table->string('marital_status')->nullable();
        $table->string('mobile')->nullable();
        $table->date('dob')->nullable();
        $table->date('join_date')->nullable();
        $table->date('confirmation_date')->nullable();
        $table->integer('probation_months')->nullable();
        $table->string('aadhaar')->nullable();
        $table->string('face_id')->nullable();
        $table->date('resignation_date')->nullable();
        $table->string('resignation_reason')->nullable();
        $table->string('department')->nullable();
        $table->string('section')->nullable();
        $table->string('designation')->nullable();
        $table->string('category')->nullable();
        $table->string('holiday_group')->nullable();
        $table->integer('hours_day')->nullable();
        $table->integer('days_week')->nullable();
        $table->integer('hours_year')->nullable();
        $table->string('employee_type')->nullable();
        $table->string('extra_classification')->nullable();
        $table->string('currency')->nullable();
        $table->string('manager')->nullable();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //  $table->unique('employee_code');
        });
    }
};
