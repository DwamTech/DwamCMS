<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تعديل enum type لإضافة القيم الجديدة
        DB::statement("ALTER TABLE backup_histories MODIFY COLUMN type ENUM('create', 'restore', 'clean', 'monitor', 'upload', 'delete', 'queued')");

        // تعديل enum status لإضافة queued
        DB::statement("ALTER TABLE backup_histories MODIFY COLUMN status ENUM('started', 'success', 'failed', 'queued')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إرجاع القيم الأصلية
        DB::statement("ALTER TABLE backup_histories MODIFY COLUMN type ENUM('create', 'restore', 'clean', 'monitor')");
        DB::statement("ALTER TABLE backup_histories MODIFY COLUMN status ENUM('started', 'success', 'failed')");
    }
};
