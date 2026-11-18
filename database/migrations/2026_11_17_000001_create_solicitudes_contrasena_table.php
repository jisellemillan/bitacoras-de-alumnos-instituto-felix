<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_contrasena', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->foreignId('user_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->enum('estado', ['pendiente', 'atendida'])->default('pendiente');
            $table->foreignId('atendida_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamp('atendida_en')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_contrasena');
    }
};
