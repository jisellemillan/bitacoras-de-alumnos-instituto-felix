<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The digital student file - one per student (created automatically on student creation)
        Schema::create('expedientes_alumnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('alumnos')->cascadeOnDelete();

            // Case type and intake
            $table->string('intake_route', 5)->nullable()->index();     // A, B, C
            $table->string('file_type', 30)->nullable();                // con_diagnostico | sin_diagnostico
            $table->string('status', 30)->default('activo')->index();   // activo | en_seguimiento | cerrado

            // Need areas (JSON array of NeedArea enum values)
            $table->json('need_areas')->nullable();

            // Primary diagnosis (stored encrypted for privacy)
            $table->text('primary_diagnosis')->nullable();

            // Strengths and support areas
            $table->text('strengths')->nullable();
            $table->text('support_areas')->nullable();

            // External specialist info (encrypted)
            $table->text('external_specialist')->nullable();

            // Treatment / medication (encrypted - most sensitive field)
            $table->text('treatment_medication')->nullable();

            // Opened/closed dates
            $table->date('opened_at')->nullable();
            $table->date('closed_at')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('usuarios')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes_alumnos');
    }
};
