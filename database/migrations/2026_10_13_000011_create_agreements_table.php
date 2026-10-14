<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acuerdos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_file_id')->constrained('expedientes_alumnos')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('alumnos')->cascadeOnDelete();
            // May link to an observation or interview
            $table->foreignId('observation_id')->nullable()->constrained('observaciones')->nullOnDelete();
            $table->foreignId('interview_id')->nullable()->constrained('entrevistas')->nullOnDelete();
            $table->foreignId('created_by')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('responsible_user_id')->nullable()->constrained('usuarios')->nullOnDelete();

            $table->text('description');                            // Agreement text
            $table->string('responsible_name')->nullable();        // Free-text name if not a system user
            $table->date('due_date')->nullable();                  // "Fecha compromiso"
            $table->string('status', 30)->default('pendiente');   // AgreementStatus enum

            $table->softDeletes();
            $table->timestamps();

            $table->index(['student_file_id', 'status']);
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acuerdos');
    }
};
