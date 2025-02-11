<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens; // Importa el trait

class Usuarios extends Model
{
    use HasFactory, HasApiTokens; // Añade HasApiTokens

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