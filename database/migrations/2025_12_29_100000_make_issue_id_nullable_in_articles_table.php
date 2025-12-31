<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('articles', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['issue_id']);

            // Make the column nullable
            $table->unsignedBigInteger('issue_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('articles', function (Blueprint $table) {
            // Revert changes (might fail if there are null values)
            $table->unsignedBigInteger('issue_id')->nullable(false)->change();
            $table->foreign('issue_id')->references('id')->on('issues')->onDelete('cascade');
        });
    }
};
