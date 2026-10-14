<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Complete audit trail - who did what, when, and to which record
        Schema::create('bitacoras_auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('action', 30);
            // created | updated | deleted | viewed | exported | printed | approved | rejected

            $table->string('auditable_type', 100);  // model class
            $table->unsignedBigInteger('auditable_id');

            $table->string('auditable_label')->nullable();  // human-readable description
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamp('created_at');

            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacoras_auditoria');
    }
};
