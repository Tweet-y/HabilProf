<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habilitacion extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'habilitacion';
    protected $primaryKey = 'id_habilitacion';

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
        return $this->hasOne(Proyecto::class, 'id_habilitacion', 'id_habilitacion');
    }

    public function prTut()
    {
        return $this->hasOne(PrTut::class, 'id_habilitacion', 'id_habilitacion');
    }
}
