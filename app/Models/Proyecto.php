<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'proyecto';
    protected $primaryKey = 'rut_alumno';
    protected $keyType = 'integer';

    protected $fillable = [
        'rut_alumno',
        'tipo_proyecto',
        'rut_profesor_guia',
        'rut_profesor_co_guia',
        'rut_profesor_comision',
    ];

    public function habilitacion()
    {
        return $this->belongsTo(Habilitacion::class, 'rut_alumno', 'rut_alumno');
    }

    public function profesorGuia()
    {
        return $this->belongsTo(Profesor::class, 'rut_profesor_guia', 'rut_profesor');
    }

    public function profesorCoGuia()
    {
        return $this->belongsTo(Profesor::class, 'rut_profesor_co_guia', 'rut_profesor');
    }

    public function profesorComision()
    {
        return $this->belongsTo(Profesor::class, 'rut_profesor_comision', 'rut_profesor');
    }
}
