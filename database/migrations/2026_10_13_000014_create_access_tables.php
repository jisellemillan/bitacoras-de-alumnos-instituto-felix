<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Access request system: users request access to student files, psicopedagogia approves
        Schema::create('solicitudes_acceso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('alumnos')->nullOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('grupos')->nullOnDelete();
            // One of student_id or group_id must be set

            $table->string('reason')->nullable();
            $table->string('status', 30)->default('pendiente');
            // pendiente | aprobado | rechazado

            $table->foreignId('reviewed_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            $table->timestamps();
            $table->index(['requester_id', 'status']);
        });

        // Granted access records with optional expiry
        Schema::create('permisos_acceso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('access_request_id')->constrained('solicitudes_acceso')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('alumnos')->nullOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('grupos')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['user_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos_acceso');
        Schema::dropIfExists('solicitudes_acceso');
    }
};
