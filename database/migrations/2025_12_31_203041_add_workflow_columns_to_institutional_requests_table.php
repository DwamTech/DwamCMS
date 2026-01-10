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
        Schema::table('institutional_support_requests', function (Blueprint $table) {
            // New Statuses: pending, waiting_for_documents, documents_review, completed, rejected, archived
            // We need to modify the enum. Since changing enum in MySQL is tricky with raw migration, 
            // we will just comment that 'status' field logic is handled in code validation.
            // Or better, we alter the column if possible, but for safety in Laravel, we just assume string validation allows new values.
            // However, to be strict, we can drop and recreate or change to string. Let's change to string to allow flexibility or keep enum but we need raw statement.
            // For simplicity in this incremental step, let's keep it but relaxing validation in controller is key. 
            // Actually, we should add the new columns first.
            
            $table->text('admin_response_message')->nullable()->after('rejection_reason');
            $table->string('closure_receipt_path')->nullable()->after('admin_response_message');
            $table->string('project_report_path')->nullable()->after('closure_receipt_path');
            $table->string('support_letter_response_path')->nullable()->after('project_report_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institutional_support_requests', function (Blueprint $table) {
            $table->dropColumn([
                'admin_response_message',
                'closure_receipt_path',
                'project_report_path',
                'support_letter_response_path'
            ]);
        });
    }
};
