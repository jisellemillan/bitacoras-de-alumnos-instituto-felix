<?php

use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;

return new class extends Migration

{

    public function up(): void

    {

        Schema::create('diagnoses', function (Blueprint $table) {

            $table->id();

            $table->foreignId('student_id')->constrained()->cascadeOnDelete();

            

            // Diagnóstico principal (ej. TDAH, Convulsiones)

            $table->string('diagnosis_name'); 

            

            // Notas sobre tratamiento médico o farmacológico

            $table->text('medical_treatment')->nullable(); 

            

            // Observaciones detalladas (contexto, terapias externas, dinámica familiar)

            $table->text('observations')->nullable(); 

            

            $table->timestamps();

        });

    }

    public function down(): void

    {

        Schema::dropIfExists('diagnoses');

    }

};
