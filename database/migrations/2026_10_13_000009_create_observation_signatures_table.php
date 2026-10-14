<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Signatures for observations (one row per signature slot)
        Schema::create('firmas_observaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('observation_id')->constrained('observaciones')->cascadeOnDelete();
            $table->string('signature_role', 40);   // SignatureRole enum value
            $table->string('signer_name')->nullable();
            $table->boolean('is_present')->default(false);
            $table->timestamps();

            $table->unique(['observation_id', 'signature_role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('firmas_observaciones');
    }
};
