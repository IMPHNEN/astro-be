<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('slug')->unique();
            $table->string('username')->unique();
            $table->string('backgroud')->nullable();
            $table->string('avatar')->nullable();
            $table->string('bio')->nullable();
            $table->string('website')->nullable();
            $table->json('socials')->nullable();
            $table->string('location')->nullable();
            $table->string('timezone')->nullable();
            $table->string('language')->nullable();
            $table->string('currency')->nullable();
            $table->string('phone')->nullable();
            $table->string('cv')->nullable();
            $table->enum('role', ['user', 'investor', 'company', 'admin'])->default('user');
            $table->string('email')->unique();
            $table->string('backup_email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
        

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
