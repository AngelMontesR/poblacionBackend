<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';

    public function direcciones()
    {
        return $this->hasMany(Direccion::class, 'id_persona', 'id');
    }


    public function telefonos()
    {
        return $this->hasMany(Telefono::class, 'id_persona', 'id');
    }

}
