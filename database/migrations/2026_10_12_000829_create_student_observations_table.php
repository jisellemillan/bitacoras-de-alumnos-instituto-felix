<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('student_observations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained()->cascadeOnDelete();
    $table->foreignId('teacher_id')->constrained('users')->restrictOnDelete();
    $table->date('observation_date');        // Cuerpo de la observación

            $table->text('observation_text');

            

            // Acciones tomadas (Checkboxes en el formato)

            $table->boolean('sent_to_prefecture')->default(false);

            $table->boolean('parent_interview_required')->default(false);

            $table->boolean('channeled')->default(false);

            

            // Acuerdos derivados de la observación

            $table->text('agreements')->nullable();

            

            $table->timestamps();

    });

}

public function down(): void

{

    Schema::dropIfExists('student_observations');

}

};

