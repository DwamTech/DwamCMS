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
        Schema::create('links', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('section_id')->constrained()->onDelete('cascade');
            $blueprint->string('title');
            $blueprint->text('description')->nullable();
            $blueprint->string('url');
            $blueprint->string('image_path')->nullable();
            $blueprint->string('keywords')->nullable();
            $blueprint->integer('views_count')->default(0);
            $blueprint->float('rating')->default(0);
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
