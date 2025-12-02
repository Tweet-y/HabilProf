<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrTut extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'pr_tut';
    protected $primaryKey = 'rut_alumno';
    protected $keyType = 'integer';

    protected $fillable = [
        'rut_alumno',
        'nombre_supervisor',
        'nombre_empresa',
        'rut_profesor_tutor',
    ];

    public function habilitacion()
    {
        return $this->belongsTo(Habilitacion::class, 'rut_alumno', 'rut_alumno');
    }

    public function profesorTutor()
    {
        return $this->belongsTo(Profesor::class, 'rut_profesor_tutor', 'rut_profesor');
    }
}
