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
        Schema::create('security_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->string('description')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // Insert default security settings
        DB::table('security_settings')->insert([
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'integer',
                'description' => 'Minimum password length',
                'group' => 'password',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'password_require_uppercase',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Require uppercase letters in password',
                'group' => 'password',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'password_require_lowercase',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Require lowercase letters in password',
                'group' => 'password',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'password_require_numbers',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Require numbers in password',
                'group' => 'password',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'password_require_symbols',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Require special symbols in password',
                'group' => 'password',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Maximum failed login attempts before lockout',
                'group' => 'login',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'lockout_duration',
                'value' => '15',
                'type' => 'integer',
                'description' => 'Account lockout duration in minutes',
                'group' => 'login',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'session_timeout',
                'value' => '120',
                'type' => 'integer',
                'description' => 'Session timeout in minutes',
                'group' => 'session',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'single_session',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Allow only one active session per user',
                'group' => 'session',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'audit_log_retention_days',
                'value' => '90',
                'type' => 'integer',
                'description' => 'Number of days to retain audit logs',
                'group' => 'audit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_settings');
    }
};
