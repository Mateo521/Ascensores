<?php
// app/Models/Ascensor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ascensor extends Model 
{
    protected $table = 'ascensors';  

       protected $fillable = [
        'codigo_interno',
        'edificio',
        'direccion',
        'numero_ascensor',
        'descripcion',
        'qr_slug',
        'estado',  
    ];

    protected $attributes = [
        'estado' => 'activo',
    ];

       protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->qr_slug)) {
                $base = Str::slug($model->codigo_interno . '-' . ($model->edificio ?? 'ascensor'));
                $slug = $base; $i = 1;
                while (static::where('qr_slug', $slug)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $model->qr_slug = $slug;
            }
        });
    }

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
