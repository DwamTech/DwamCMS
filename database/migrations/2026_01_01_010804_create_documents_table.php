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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // اسم الملف
            $table->text('description')->nullable(); // وصف

            // File Management
            $table->enum('source_type', ['file', 'link'])->default('file'); // رابط أو ملف مرفوع
            $table->string('file_path')->nullable(); // رابط الملف المرفوع
            $table->text('source_link')->nullable(); // رابط خارجي

            // Cover Image
            $table->enum('cover_type', ['auto', 'upload'])->default('auto');
            $table->string('cover_path')->nullable(); // كفر الملف

            // Metadata
            $table->text('keywords')->nullable(); // كلمات دلالية (JSON)
            $table->string('file_type')->nullable(); // نوع الملف (pdf, docx, etc.)
            $table->bigInteger('file_size')->nullable(); // حجم الملف بالبايت
            $table->bigInteger('views_count')->default(0);
            $table->bigInteger('downloads_count')->default(0);

            // Owner
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // مالك

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
