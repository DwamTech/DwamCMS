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
        Schema::create('individual_support_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->string('full_name');
            $table->enum('gender', ['male', 'female']);
            $table->string('nationality');
            $table->string('city');
            $table->string('housing_type');
            $table->string('housing_type_other')->nullable();
            $table->string('identity_image_path');
            $table->date('birth_date');
            $table->date('identity_expiry_date');
            $table->string('phone_number');
            $table->string('whatsapp_number');
            $table->string('email');
            $table->string('academic_qualification_path');
            $table->string('scientific_activity');
            $table->string('scientific_activity_other')->nullable();
            $table->string('cv_path');
            $table->string('workplace');
            $table->enum('support_scope', ['full', 'partial']);
            $table->decimal('amount_requested', 10, 2);
            $table->string('support_type');
            $table->string('support_type_other')->nullable();
            $table->boolean('has_income')->default(false);
            $table->string('income_source')->nullable();
            $table->enum('marital_status', ['single', 'married']);
            $table->integer('family_members_count')->nullable();
            $table->string('recommendation_path')->nullable();
            $table->string('bank_account_iban');
            $table->string('bank_name');
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
        Schema::dropIfExists('individual_support_requests');
    }
};
