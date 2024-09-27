<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistroEspecificacion extends Model
{
    use SoftDeletes;
    protected $fillable = ['especificacion_id', 'descripcion'];
    protected $table = 'registroespecificacion';
}
