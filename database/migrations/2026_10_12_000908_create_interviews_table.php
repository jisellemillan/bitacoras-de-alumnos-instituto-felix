<?php

use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;

return new class extends Migration

{

    public function up(): void

    {

        Schema::create('interviews', function (Blueprint $table) {

            $table->id();

            $table->foreignId('student_id')->constrained()->cascadeOnDelete();

            $table->foreignId('user_id')->constrained('users')->comment('El psicopedagogo o profesor que realiza la entrevista');

            $table->date('interview_date');

            

            // Modalidad

            $table->enum('modality', ['presencial', 'en_linea', 'telefonica']);

            

            $table->text('motive_objective');

            $table->text('comments');

            

            $table->timestamps();

        });

    }

    public function down(): void

    {

        Schema::dropIfExists('interviews');

    }

};
