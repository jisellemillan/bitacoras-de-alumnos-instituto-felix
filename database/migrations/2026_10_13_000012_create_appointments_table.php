<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_file_id')->constrained('expedientes_alumnos')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('usuarios')->cascadeOnDelete();
            // May be created from an observation's "Próxima cita" field
            $table->foreignId('observation_id')->nullable()->constrained('observaciones')->nullOnDelete();

            $table->string('title')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('location')->nullable();
            $table->string('status', 30)->default('programada');    // programada | realizada | cancelada

            // Who needs to be present (JSON array of SignatureRole values)
            $table->json('required_attendees')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['starts_at', 'status']);
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
