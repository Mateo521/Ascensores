<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('revisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ascensor_id')->constrained('ascensors')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->date('fecha');
            $table->string('estado')->default('pendiente');  
            $table->json('formulario')->nullable(); 
            $table->text('observaciones')->nullable();
            $table->boolean('sincronizado')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('revisiones');
    }
};
