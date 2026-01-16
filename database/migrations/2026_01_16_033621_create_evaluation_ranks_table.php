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
        Schema::create('evaluation_ranks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('council_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('final_score', 5, 3)->nullable();
            $table->enum('rank', ['gold', 'silver', 'bronze', 'none'])->nullable();
            $table->enum('status', ['pending', 'finalized'])->default('pending');
            $table->timestamps();
            
            $table->unique(['evaluation_id', 'user_id']);
            $table->index(['evaluation_id', 'council_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_ranks');
    }
};
