<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    use HasFactory;
    
    protected $table = 'profesors';
    protected $primaryKey = 'rut_profesor';
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'rut_profesor',
        'nombre_profesor',
        'apellido_profesor',
    ];

    public function proyectosComoGuia()
    {
        return $this->hasMany(Proyecto::class, 'rut_profesor_guia', 'rut_profesor');
    }

    public function proyectosComoCoGuia()
    {
        return $this->hasMany(Proyecto::class, 'rut_profesor_co_guia', 'rut_profesor');
    }

    public function proyectosComoComision()
    {
        return $this->hasMany(Proyecto::class, 'rut_profesor_comision', 'rut_profesor');
    }

    public function prTutsComoTutor()
    {
        return $this->hasMany(PrTut::class, 'rut_profesor_tutor', 'rut_profesor');
    }
}
