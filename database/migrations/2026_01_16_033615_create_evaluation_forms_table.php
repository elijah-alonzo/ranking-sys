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
        Schema::create('evaluation_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('evaluator_type', ['adviser', 'peer', 'self']);
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('answers')->nullable();
            $table->decimal('evaluator_score', 5, 3)->nullable();
            $table->timestamps();
            
            $table->unique(['evaluation_id', 'user_id', 'evaluator_type']);
            $table->index(['evaluation_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_forms');
    }
};
