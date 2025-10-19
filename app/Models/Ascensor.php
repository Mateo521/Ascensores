<?php
// app/Models/Ascensor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ascensor extends Model 
{
    protected $table = 'ascensors'; // ← Asegúrate que esté

    protected $fillable = [
        'codigo_interno',
        'edificio',
        'direccion',
        'numero_ascensor',
        'descripcion',
        'qr_slug',
        'estado'
    ];

    protected $attributes = [
        'estado' => 'activo',
    ];

    public function revisiones()
    { 
        return $this->hasMany(Revision::class); 
    }

    // Relación para obtener la última revisión
    public function ultimaRevision()
    {
        return $this->hasOne(Revision::class)->latestOfMany();
    }

    // Relación para revisiones pendientes
    public function revisionesPendientes()
    {
        return $this->hasMany(Revision::class)->where('estado', 'pendiente');
    }

    // Relación para revisiones completadas
    public function revisionesCompletadas()
    {
        return $this->hasMany(Revision::class)->where('estado', 'completada');
    }
}
