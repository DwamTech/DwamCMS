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
        Schema::table('individual_support_requests', function (Blueprint $table) {
            $table->text('admin_response_message')->nullable()->after('rejection_reason');
            $table->string('closure_receipt_path')->nullable()->after('admin_response_message');
            $table->string('project_report_path')->nullable()->after('closure_receipt_path'); // Though maybe less relevant for individuals, keeping structure consistent if needed
            // Individuals might just need receipt, but user asked for "Trip" generally.
            // Let's assume individuals also might need to upload proof of need fulfillment.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('individual_support_requests', function (Blueprint $table) {
            $table->dropColumn([
                'admin_response_message',
                'closure_receipt_path',
                'project_report_path',
            ]);
        });
    }
};
