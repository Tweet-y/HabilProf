<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestionAcademica extends Model
{
    use HasFactory;

    protected $table = 'gestion_academica';
    protected $primaryKey = 'rut_profesor';
    public $incrementing = false;

    protected $fillable = [
        'rut_profesor',
        'nombre_profesor',
        'apellido_profesor',
        'departamento'
    ];

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'rut_profesor', 'rut_profesor');
    }
}