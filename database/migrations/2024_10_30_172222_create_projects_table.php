<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private $status = ['pending', 'review', 'waiting_approval', 'accepted', 'rejected'];
    /**
     * Run the migrations.
     */
    public function up(): void {

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->default(sha1(random_bytes(10)));
            $table->string('title');
            $table->string('submiter')->constraint('users', 'id');
            $table->string('description');
            $table->string('proposal');
            $table->enum('status', $this->status)->default('pending');
            $table->string('category')->nullable();
            $table->string('expected_price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('projects');
    }
};
