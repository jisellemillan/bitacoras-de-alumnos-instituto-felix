<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_file_id')->constrained('expedientes_alumnos')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('usuarios')->cascadeOnDelete();

            $table->string('title');
            $table->string('description')->nullable();
            $table->string('file_path');                // stored in storage/app/private
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();  // bytes
            $table->string('category', 50)->nullable();
            // diagnostico_externo | informe | evaluacion | otro

            $table->softDeletes();
            $table->timestamps();

            $table->index('student_file_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
