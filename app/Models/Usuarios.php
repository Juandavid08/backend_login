<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    use HasFactory;

    protected $table = 'Usuarios'; 

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'telefono',
        'password'
    ];

    protected $hidden = [
        'password' // Para que no se muestre en las respuestas JSON
    ];
}