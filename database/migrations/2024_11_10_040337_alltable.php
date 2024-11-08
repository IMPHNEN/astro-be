<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {    
        // Projects table
        Schema::create('projects', function (Blueprint $table) {
            $table->id('project_id');
            $table->string('slug')->unique();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->string('project_name');
            $table->decimal('budget', 15, 2);
            $table->date('deadline')->nullable();
            $table->text('description')->nullable();
            $table->text('proposal')->nullable();
            $table->text('requirements')->nullable();
            $table->enum('status', ['open', 'in_progress', 'completed'])->default('open');
            $table->timestamps();
        });
    
        // Project Applications table
        Schema::create('project_applications', function (Blueprint $table) {
            $table->id('application_id');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamp('applied_at')->useCurrent();
        });
    
        // Investments table
        Schema::create('investments', function (Blueprint $table) {
            $table->id('investment_id');
            $table->foreignId('investor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->timestamp('invested_at')->useCurrent();
        });
    
        // Milestones table
        Schema::create('milestones', function (Blueprint $table) {
            $table->id('milestone_id');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('color')->default('#ffe600');
            $table->string('assigned_to')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', ['backlog', 'in_progress', 'testing', 'awaiting_review',  'completed', 'dropped'])->default('backlog');
            $table->timestamps();
        });
    
        // Skills table
        Schema::create('skills', function (Blueprint $table) {
            $table->id('skill_id');
            $table->string('skill_name');
        });
    
        // User Skills table
        Schema::create('user_skills', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
            $table->primary(['user_id', 'skill_id']);
        });
    
        // Pitch Decks table
        Schema::create('pitch_decks', function (Blueprint $table) {
            $table->id('pitch_id');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('document_path');
            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pitch_decks');
        Schema::dropIfExists('user_skills');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('investments');
        Schema::dropIfExists('project_applications');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('users');
    }
};