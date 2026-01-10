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
        Schema::create('site_contacts', function (Blueprint $table) {
            $table->id();

            // Social Media Links
            $table->string('youtube')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->string('snapchat')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();

            // Phone Numbers
            $table->string('support_phone')->nullable();       // رقم الدعم الفني
            $table->string('management_phone')->nullable();    // رقم إدارة المشروع
            $table->string('backup_phone')->nullable();        // رقم احتياطي

            // Business Details
            $table->string('address')->nullable();             // المقر
            $table->string('commercial_register')->nullable(); // رقم السجل التجاري
            $table->string('email')->nullable();               // البريد الإلكتروني

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_contacts');
    }
};
