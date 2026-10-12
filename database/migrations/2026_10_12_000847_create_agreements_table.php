<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
Schema::create('agreements', function (Blueprint $table) {
$table->id();
$table->foreignId('student_id')->constrained()->cascadeOnDelete();        $table->text('agreement_text');

        $table->date('next_appointment_date')->nullable();

        $table->string('required_presence')->nullable()->comment('Es necesario la presencia de...');

        

        // Control de firmas/aprobaciones digitales (simulando los campos de firma física)

        $table->boolean('signed_by_parent')->default(false);

        $table->boolean('signed_by_teacher_spanish')->default(false);

        $table->boolean('signed_by_teacher_english')->default(false);

        $table->boolean('signed_by_psychopedagogy')->default(false);

        $table->boolean('signed_by_specialist')->default(false);

        $table->boolean('signed_by_primary_director')->default(false);

        $table->boolean('signed_by_academic_coordinator')->default(false);

        

        $table->timestamps();

    });

}

public function down(): void

{

    Schema::dropIfExists('agreements');

}

};
