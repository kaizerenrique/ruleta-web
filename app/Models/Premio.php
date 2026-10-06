<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Premio extends Model
{
    protected $fillable = ['nombre', 'color', 'icono', 'probabilidad', 'es_premio', 'activo'];
    public function resultados() { return $this->hasMany(Resultado::class); }
}
