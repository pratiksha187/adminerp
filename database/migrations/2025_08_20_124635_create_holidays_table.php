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
        Schema::create('holidays', function (Blueprint $table) {
          $table->id();
            $table->date('date');
            $table->string('title');
            $table->string('type')->nullable();   // public/company/weekly_off
            $table->string('color')->nullable();  // optional custom color
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['date', 'title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
