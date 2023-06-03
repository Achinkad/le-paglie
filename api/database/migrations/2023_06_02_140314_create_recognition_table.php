<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recognitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('camera_id');
                $table->foreign('camera_id')->references('id')->on('cameras')->onDelete('cascade');
            $table->string('name');
            $table->json('photos');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recognitions');
    }
};
