<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entrevistas', function (Blueprint $table) {
            $table->integer('id')->nullable();
            $table->unsignedBigInteger('vacante');
            $table->unsignedBigInteger('prospecto');
            $table->date('fecha_entrevista')->nullable();
            $table->text('notas')->nullable();
            $table->boolean('reclutado')->nullable();
        
            // clave primaria compuesta
            $table->primary(['vacante', 'prospecto']);
        
            // claves foráneas
            $table->foreign('vacante')->references('id')->on('vacantes');
            $table->foreign('prospecto')->references('id')->on('prospectos');
        
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrevistas');
    }
};
