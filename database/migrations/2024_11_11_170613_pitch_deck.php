<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('pitch_decks', function (Blueprint $table) {
            $table->id('pitch_id'); // Primary key
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade'); // Foreign key to projects table
            $table->string('document_path'); // Path to the document
            $table->timestamp('uploaded_at')->useCurrent(); // Timestamp for when the document was uploaded
        });
    }

    public function down() {
        Schema::dropIfExists('pitch_decks');
    }
};