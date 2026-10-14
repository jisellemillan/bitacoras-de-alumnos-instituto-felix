<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);

            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'sqlite') {
                $table->string('full_name', 210)->virtualAs("first_name || ' ' || last_name");
            } else {
                $table->string('full_name', 210)->virtualAs("CONCAT(first_name, ' ', last_name)");
            }

            $table->date('birthdate')->nullable();
            $table->string('sex', 10)->nullable();              // masculino | femenino | otro
            $table->string('curp', 18)->nullable()->unique();
            $table->string('photo_path')->nullable();           // stored in storage/app/private
            $table->boolean('is_active')->default(true)->index();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['last_name', 'first_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
