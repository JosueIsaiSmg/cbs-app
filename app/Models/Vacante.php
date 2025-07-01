<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Factories\HasFactory;

class Vacante extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'area', 'sueldo', 'activo',
    ];

    public function entrevistas()
    {
        return $this->hasMany(Entrevista::class, 'vacante');
    }
}
