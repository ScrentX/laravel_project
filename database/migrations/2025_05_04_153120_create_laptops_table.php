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
        Schema::create('laptops', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('brand');
            $table->string('model');
            $table->enum('status', ['Available', 'Reserved', 'Rented', 'Maintenance', 'Unusable'])->default('Available');
            $table->enum('condition', ['Excellent','Best', 'Good', 'Frequently Used'])->default('Excellent');
            $table->string('image_path')->nullable();
            $table->date('acquired_date');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('create_laptops_tables');
    }
};
