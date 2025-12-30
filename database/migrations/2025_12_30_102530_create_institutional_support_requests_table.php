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
        Schema::create('institutional_support_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->string('institution_name');
            $table->string('license_number');
            $table->string('license_certificate_path');
            $table->string('email');
            $table->string('support_letter_path');
            $table->string('phone_number');
            $table->string('ceo_name');
            $table->string('ceo_mobile');
            $table->string('whatsapp_number');
            $table->string('city');
            $table->string('activity_type');
            $table->string('activity_type_other')->nullable();
            $table->string('project_name');
            $table->string('project_type');
            $table->string('project_type_other')->nullable();
            $table->string('project_file_path');
            $table->string('project_manager_name');
            $table->string('project_manager_mobile');
            $table->string('goal_1');
            $table->string('goal_2')->nullable();
            $table->string('goal_3')->nullable();
            $table->string('goal_4')->nullable();
            $table->text('other_goals')->nullable();
            $table->string('beneficiaries');
            $table->string('beneficiaries_other')->nullable();
            $table->decimal('project_cost', 15, 2);
            $table->text('project_outputs');
            $table->string('operational_plan_path');
            $table->enum('support_scope', ['full', 'partial']);
            $table->decimal('amount_requested', 15, 2);
            $table->string('account_name');
            $table->string('bank_account_iban');
            $table->string('bank_name');
            $table->string('bank_certificate_path');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutional_support_requests');
    }
};
