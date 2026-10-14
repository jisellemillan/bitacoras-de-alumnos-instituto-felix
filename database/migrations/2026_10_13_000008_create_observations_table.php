<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Replica del formato físico "Observación de alumno"
        Schema::create('observaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_file_id')->constrained('expedientes_alumnos')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('school_cycle_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('usuarios')->cascadeOnDelete();

            // Header fields from physical format
            $table->date('observation_date');               // "Mexicali, B.C., a __ de __ del 20__"
            $table->string('grade_group', 20)->nullable();  // "Grado y grupo" - snapshot at time of writing

            // Main body
            $table->text('observations');                   // large text box "Observaciones:"

            // Checkboxes: "Fue necesario:"
            $table->boolean('required_prefectura')->default(false);
            $table->boolean('required_parent_interview')->default(false);
            $table->boolean('required_channeling')->default(false);

            // "Acuerdos:"
            $table->text('agreements_text')->nullable();

            // "Próxima cita:" - creates an appointment
            $table->date('next_appointment_date')->nullable();

            // "Es necesario la presencia de:"
            $table->text('required_presence')->nullable();

            // "Si están presentes:" (who was actually present)
            $table->text('attendees')->nullable();

            // "Nombre y firma del maestro que elabora"
            $table->string('elaborated_by_name')->nullable();

            // Draft support (autosave)
            $table->boolean('is_draft')->default(false)->index();

            // Optimistic locking for concurrent edits
            $table->unsignedInteger('lock_version')->default(0);

            $table->softDeletes();
            $table->timestamps();

            $table->index(['student_file_id', 'observation_date']);
            $table->index(['school_cycle_id', 'created_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observaciones');
    }
};
