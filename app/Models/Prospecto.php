<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Factories\HasFactory;

class Prospecto extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'nombre', 'correo', 'fecha_registro',
    ];
}
