<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Factories\HasFactory;


class Entrevista extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'vacante', 'prospecto', 'fecha_entrevista', 'notas', 'reclutado',
    ];

    public function vacante()
    {
        return $this->belongsTo(Vacante::class, 'vacante');
    }

    public function prospecto()
    {
        return $this->belongsTo(Prospecto::class, 'prospecto');
    }
}
