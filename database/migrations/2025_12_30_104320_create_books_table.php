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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('source_type', ['file', 'link', 'embed']);
            $table->string('file_path')->nullable();
            $table->text('source_link')->nullable(); // For link or embed code
            $table->enum('cover_type', ['auto', 'upload']);
            $table->string('cover_path')->nullable();
            $table->text('keywords')->nullable(); // JSON or comma separated
            $table->bigInteger('views_count')->default(0);
            $table->decimal('rating_sum', 8, 2)->default(0);
            $table->bigInteger('rating_count')->default(0);
            $table->string('author_name');
            $table->enum('type', ['single', 'part']);
            $table->foreignId('book_series_id')->nullable()->constrained('book_series')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
