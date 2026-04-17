<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pharmacy_id')
                ->unique()
                ->constrained('pharmacies')
                ->cascadeOnDelete();

            // Conditions de paiement (ex: "Net 30", "Comptant", "50% à la commande"...)
            $table->text('payment_terms')->nullable();

            // Limite de crédit en montant monétaire
            $table->decimal('credit_limit', 12, 2)->default(0);

            // Commercial assigné (rôle "Commercial")
            $table->foreignId('commercial_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Statut du client
            $table->enum('status', ['actif', 'inactif'])->default('actif');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

