<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained('zones')->cascadeOnDelete();
            $table->string('name');
            $table->string('owner_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->enum('type', ['publique', 'privée', 'clinique'])->default('privée');
            $table->text('best_selling_products')->nullable();
            $table->boolean('stock_problem')->default(false);
            $table->boolean('delivery_problem')->default(false);
            $table->boolean('training_need')->default(false);
            $table->boolean('distribution_need')->default(false);
            $table->enum('interest_status', ['non_visité', 'visité', 'intéressé', 'non_intéressé', 'client'])->default('non_visité');
            $table->enum('partnership_type', ['aucun', 'distributeur', 'partenaire', 'client_direct'])->default('aucun');
            $table->text('notes')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacies');
    }
};
