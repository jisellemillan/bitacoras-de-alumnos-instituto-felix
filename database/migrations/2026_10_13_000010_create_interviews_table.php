<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Replica del formato físico "Entrevista con padres y/o especialista"
        Schema::create('entrevistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_file_id')->constrained('expedientes_alumnos')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('school_cycle_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('usuarios')->cascadeOnDelete();

            // Header
            $table->date('interview_date');
            $table->string('grade_group', 20)->nullable();

            // Modalidad: presencial | en_linea | via_telefonica
            $table->string('modality', 30)->nullable();

            // Two main text boxes from the physical format
            $table->text('purpose_and_objective');   // "Motivo y objetivo:"
            $table->text('comments')->nullable();    // "Comentarios:"

            // Attendees and who conducted the interview
            $table->text('attendees')->nullable();
            $table->string('conducted_by_name')->nullable();

            // Draft and versioning
            $table->boolean('is_draft')->default(false)->index();
            $table->unsignedInteger('lock_version')->default(0);

            $table->softDeletes();
            $table->timestamps();

            $table->index(['student_file_id', 'interview_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrevistas');
    }
};
