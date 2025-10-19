<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ascensors', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_interno')->unique();
            $table->string('edificio')->nullable();
            $table->string('direccion')->nullable();
            $table->string('numero_ascensor')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('qr_slug')->unique();
            $table->string('estado')->default('activo');  
            $table->timestamps();
        });
    }
    
    public function down(): void {
        Schema::dropIfExists('ascensors');
    }
};
