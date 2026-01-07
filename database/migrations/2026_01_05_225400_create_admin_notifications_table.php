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
        if (Schema::hasTable('admin_notifications')) {
            return;
        }

        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();

            // Notification type and category
            $table->string('type');         // e.g., 'new_individual_support', 'backup_failed'
            $table->string('category');     // e.g., 'support', 'system', 'users', 'content'

            // Content
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();  // Additional data (request_id, user_name, etc.)

            // Priority: low, medium, high
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');

            // Status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            // Action URL (where to go when clicked)
            $table->string('action_url')->nullable();

            // Who triggered this notification (optional)
            $table->unsignedBigInteger('triggered_by')->nullable();
            $table->foreign('triggered_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();

            // Indexes for faster queries
            $table->index('is_read');
            $table->index('category');
            $table->index('priority');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
