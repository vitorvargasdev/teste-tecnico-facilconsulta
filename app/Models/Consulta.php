<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Consulta extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'medico_id',
        'paciente_id',
        'data',
    ];

    protected $hidden = [
        'medico_id',
        'paciente_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function paciente(): HasOne
    {
        return $this->hasOne(Paciente::class);
    }

    public function medico(): HasOne
    {
        return $this->hasOne(Medico::class);
    }
}
