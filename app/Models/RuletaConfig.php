<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuletaConfig extends Model
{
    protected $fillable = ['max_giros_por_usuario', 'captcha_activo'];
}
