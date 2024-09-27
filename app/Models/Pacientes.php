<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pacientes extends Model
{
    use SoftDeletes;
    protected $fillable = ['nombres', "Estatus"];
    protected $table = 'pacientes';
}
