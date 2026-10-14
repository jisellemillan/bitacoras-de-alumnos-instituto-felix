<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot: which teacher is assigned to which group and in what capacity
        Schema::create('grupo_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('assignment_type', 30)->default('titular');
            // titular | espanol | ingles | educacion_fisica | arte | otra_materia
            $table->timestamps();

            $table->unique(['group_id', 'user_id', 'assignment_type']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_usuario');
    }
};
