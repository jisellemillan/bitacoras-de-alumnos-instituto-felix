<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_cycle_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->unsignedTinyInteger('grade');   // 4, 5, 6
            $table->string('section', 5);            // A, B, C...
            $table->timestamps();

            $table->unique(['school_cycle_id', 'grade', 'section']);
            $table->index(['grade', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
