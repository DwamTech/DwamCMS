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
        Schema::table('admin_notifications', function (Blueprint $table) {
            // Check and add missing columns
            if (! Schema::hasColumn('admin_notifications', 'type')) {
                $table->string('type')->after('id');
            }
            if (! Schema::hasColumn('admin_notifications', 'category')) {
                $table->string('category')->after('type');
            }
            if (! Schema::hasColumn('admin_notifications', 'title')) {
                $table->string('title')->after('category');
            }
            if (! Schema::hasColumn('admin_notifications', 'message')) {
                $table->text('message')->after('title');
            }
            if (! Schema::hasColumn('admin_notifications', 'data')) {
                $table->json('data')->nullable()->after('message');
            }
            if (! Schema::hasColumn('admin_notifications', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('data');
            }
            if (! Schema::hasColumn('admin_notifications', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('priority');
            }
            if (! Schema::hasColumn('admin_notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('is_read');
            }
            if (! Schema::hasColumn('admin_notifications', 'action_url')) {
                $table->string('action_url')->nullable()->after('read_at');
            }
            if (! Schema::hasColumn('admin_notifications', 'triggered_by')) {
                $table->unsignedBigInteger('triggered_by')->nullable()->after('action_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا نحذف الأعمدة لأنها قد تحتوي على بيانات
    }
};
