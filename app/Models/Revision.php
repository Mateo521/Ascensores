<?php
// app/Models/Revision.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    protected $table = 'revisiones'; // ← ASEGÚRATE QUE ESTA LÍNEA ESTÉ

    protected $fillable = [
        'ascensor_id',
        'user_id',
        'fecha',
        'estado',
        'formulario',
        'observaciones',
        'sincronizado'
    ];

    protected $casts = [
        'fecha' => 'date',
        'formulario' => 'array',
        'sincronizado' => 'boolean',
    ];

    protected $attributes = [
        'estado' => 'pendiente',
        'sincronizado' => false,
    ];

    public function ascensor()
    {
        return $this->belongsTo(Ascensor::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope para revisiones pendientes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    // Scope para revisiones completadas
    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    // Scope para revisiones del mes actual
    public function scopeMesActual($query)
    {
        return $query->whereMonth('fecha', now()->month)
                    ->whereYear('fecha', now()->year);
    }
}
