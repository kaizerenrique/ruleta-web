<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aplicacion extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
