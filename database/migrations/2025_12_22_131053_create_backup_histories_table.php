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
        if (Schema::hasTable('backup_histories')) {
            return;
        }

        Schema::create('backup_histories', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['create', 'restore', 'clean', 'monitor']);
            $table->enum('status', ['started', 'success', 'failed']);
            $table->string('file_name')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->text('message')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_histories');
    }
};
