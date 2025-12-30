<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (! Schema::hasColumn('articles', 'issue_id')) {
                return;
            }

            try {
                $table->dropForeign(['issue_id']);
            } catch (\Throwable $e) {
            }

            try {
                $table->dropIndex(['issue_id']);
            } catch (\Throwable $e) {
            }

            $table->unsignedBigInteger('issue_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (! Schema::hasColumn('articles', 'issue_id')) {
                return;
            }

            $table->unsignedBigInteger('issue_id')->nullable(false)->change();
            $table->foreign('issue_id')->references('id')->on('issues')->onDelete('cascade');
        });
    }
};
