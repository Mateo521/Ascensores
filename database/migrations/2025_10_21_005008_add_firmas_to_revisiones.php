<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('revisiones', function (Blueprint $table) {
            $table->string('firma_tecnico_path')->nullable()->after('formulario');
            $table->string('firma_tecnico_nombre')->nullable()->after('firma_tecnico_path');
            $table->string('firma_cliente_path')->nullable()->after('firma_tecnico_nombre');
            $table->string('firma_cliente_nombre')->nullable()->after('firma_cliente_path');
            $table->timestamp('firmado_at')->nullable()->after('observaciones');
            $table->string('verificacion_hash', 64)->nullable()->unique()->after('firmado_at');
        });
    }
    public function down(): void
    {
        Schema::table('revisiones', function (Blueprint $table) {
            $table->dropColumn([
                'firma_tecnico_path',
                'firma_tecnico_nombre',
                'firma_cliente_path',
                'firma_cliente_nombre',
                'firmado_at',
                'verificacion_hash',
            ]);
        });
    }
};
