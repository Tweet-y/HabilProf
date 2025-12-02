<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habilitacion extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'habilitacion';
    protected $primaryKey = 'rut_alumno';
    protected $keyType = 'integer';

    protected $fillable = [
        'rut_alumno',
        'nota_final',
        'fecha_nota',
        'semestre_inicio',
        'descripcion',
        'titulo',
    ];

    protected $casts = [
        'nota_final' => 'float',
        'fecha_nota' => 'date',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'rut_alumno', 'rut_alumno');
    }

    public function proyecto()
    {
        return $this->hasOne(Proyecto::class, 'rut_alumno', 'rut_alumno');
    }

    public function prTut()
    {
        return $this->hasOne(PrTut::class, 'rut_alumno', 'rut_alumno');
    }
}
