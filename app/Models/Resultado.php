<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    protected $fillable = ['nick', 'nombre', 'apellido', 'premio_id', 'premio_nombre', 'ip', 'fecha'];
    protected $casts = ['fecha' => 'datetime'];
    public function premio() { return $this->belongsTo(Premio::class); }
}
