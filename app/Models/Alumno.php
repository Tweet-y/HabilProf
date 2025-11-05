<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'alumno';
    protected $primaryKey = 'rut_alumno';
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'rut_alumno',
        'nombre_alumno',
        'apellido_alumno',
    ];

    public function habilitacion()
    {
        return $this->hasOne(Habilitacion::class, 'rut_alumno', 'rut_alumno');
    }
}
