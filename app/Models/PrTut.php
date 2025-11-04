<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrTut extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'pr_tut';
    protected $primaryKey = 'id_habilitacion';
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'id_habilitacion',
        'nombre_supervisor',
        'nombre_empresa',
        'rut_profesor_tutor',
    ];

    public function habilitacion()
    {
        return $this->belongsTo(Habilitacion::class, 'id_habilitacion', 'id_habilitacion');
    }

    public function tutor()
    {
        return $this->belongsTo(Profesor::class, 'rut_profesor_tutor', 'rut_profesor');
    }
}
