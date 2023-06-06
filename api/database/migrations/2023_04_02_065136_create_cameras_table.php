<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cameras', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('authorization'); // Base64 username:password
            $table->string('ip_address');
            $table->string('location')->nullable();
            $table->string('pet_identification')->default(false);
            $table->string('pet_name')->nullable();
            $table->string('pet_action')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cameras');
    }
};
