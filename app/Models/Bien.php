<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Bien extends Model
{
    use HasFactory;
    use Auditable;

    protected $table = 'biens';

    protected $fillable = [
        'codigo_uni',
        'nombre',
        'descripcion',
        'imagen',
        'pnf_id',
    ];

    // 1. Incluimos el accessor en la serialización JSON del modelo
    protected $appends = ['imagen_url'];

    // 2. Definimos el Accessor
    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            // Genera la URL pública completa (Ej: http://localhost:8000/storage/bienes/foto.jpg)
            return Storage::disk('public')->url($this->imagen);
        }

        return null;
    }

    /**
     * Relación con PNF.
     */
    public function pnf()
    {
        return $this->belongsTo(Pnf::class, 'pnf_id');
    }

    /**
     * Historia de reportes de estado del bien.
     */
    public function reportesEstado()
    {
        return $this->hasMany(ReporteEstado::class, 'bien_id');
    }

    /**
     * Obtener el último reporte de estado registrado.
     */
    public function ultimoReporte()
    {
        return $this->hasOne(ReporteEstado::class, 'bien_id')->latestOfMany();
    }
}
