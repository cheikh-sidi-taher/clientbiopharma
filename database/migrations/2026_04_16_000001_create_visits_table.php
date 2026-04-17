<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pharmacy_id')->constrained('pharmacies')->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();

            // Date planifiée (planning hebdo)
            $table->date('scheduled_date');

            $table->enum('status', ['planifié', 'réalisé', 'annulé'])->default('planifié');
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['pharmacy_id', 'scheduled_date', 'agent_id'], 'visits_unique_slot');
            $table->index(['agent_id', 'scheduled_date'], 'visits_agent_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};

