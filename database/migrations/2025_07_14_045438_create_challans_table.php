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
        Schema::create('challans', function (Blueprint $table) {
            $table->id();
            $table->string('challan_no');
            $table->date('date');
            $table->string('party_name');
            $table->string('material');
            $table->string('vehicle_no')->nullable();
            $table->string('measurement')->nullable();
            $table->string('location')->nullable();
            $table->string('time')->nullable();
            $table->string('receiver_sign')->nullable();
            $table->string('driver_sign')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challans');
    }
};
