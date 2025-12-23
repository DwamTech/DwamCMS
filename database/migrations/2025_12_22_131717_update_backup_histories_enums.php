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
        Schema::table('backup_histories', function (Blueprint $table) {
            // Using DB::statement because Laravel schema builder doesn't support modifying enums elegantly in all drivers
            // This is for MySQL/MariaDB
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE backup_histories MODIFY COLUMN type ENUM('create', 'restore', 'clean', 'monitor', 'upload') NOT NULL");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE backup_histories MODIFY COLUMN status ENUM('started', 'success', 'failed', 'queued') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backup_histories', function (Blueprint $table) {
            //
        });
    }
};
