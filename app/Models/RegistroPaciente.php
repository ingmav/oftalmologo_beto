<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistroPaciente extends Model
{
    use SoftDeletes;
    protected $fillable = [''];
    protected $table = 'registropaciente';

    public function cliente(){
        return $this->belongsto('App\Models\Pacientes','paciente_id')->withTrashed();
    }

    public function especificaciones(){
        return $this->hasMany('App\Models\RegistroEspecificacion');
    }
}
