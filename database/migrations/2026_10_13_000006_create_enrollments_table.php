<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Historical enrollment: student ↔ group ↔ cycle (multi-cycle traceability)
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('school_cycle_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->boolean('is_current')->default(false)->index();
            $table->timestamps();

            $table->unique(['student_id', 'school_cycle_id']);
            $table->index(['group_id', 'school_cycle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
