<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'proyecto';
    protected $primaryKey = 'id_habilitacion';
    public $incrementing = false;
    protected $keyType = 'integer';

    const Tipo_Pr_Ing ='PrIng';
    const Tipo_Pr_Inv ='PrInv';
    const Tipo_Pr_Tut ='PrTut';

    public static function getTiposProyecto()
    {
        return [
            self::Tipo_Pr_Ing,
            self::Tipo_Pr_Inv,
            self::Tipo_Pr_Tut,
        ];
    }



    protected $fillable = [
        'id_habilitacion',
        'tipo_proyecto',
        'rut_profesor_guia',
        'rut_profesor_co_guia',
        'rut_profesor_comision',
    ];

    public function getTipoProyectoAttribute($value)
    {
        if (!in_array($value, self::getTiposProyecto())) {
            throw new \InvalidArgumentException("Tipo de proyecto inválido: $value");
        }
        return $value;
    }

    public function habilitacion()
    {
        return $this->belongsTo(Habilitacion::class, 'id_habilitacion', 'id_habilitacion');
    }

    public function guia()
    {
        return $this->belongsTo(Profesor::class, 'rut_profesor_guia', 'rut_profesor');
    }

    public function coGuia()
    {
        return $this->belongsTo(Profesor::class, 'rut_profesor_co_guia', 'rut_profesor');
    }

    public function comision()
    {
        return $this->belongsTo(Profesor::class, 'rut_profesor_comision', 'rut_profesor');
    }
}
