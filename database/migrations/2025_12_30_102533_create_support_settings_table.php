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
        Schema::create('support_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->timestamps();
        });

        // Insert Default Settings
        DB::table('support_settings')->insert([
            ['key' => 'individual_support_enabled', 'value' => 'true', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'institutional_support_enabled', 'value' => 'true', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_settings');
    }
};
