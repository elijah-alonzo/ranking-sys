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
        Schema::create('evaluation_peer_evaluators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->foreignId('evaluatee_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('evaluator_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by_user_id')->constrained('users')->onDelete('cascade');
            $table->text('assignment_notes')->nullable();
            $table->timestamp('assigned_at');
            $table->timestamps();
            
            // Ensure unique evaluator-evaluatee combination per evaluation
            $table->unique(['evaluation_id', 'evaluator_user_id', 'evaluatee_user_id'], 'unique_peer_evaluation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_peer_evaluators');
    }
};
