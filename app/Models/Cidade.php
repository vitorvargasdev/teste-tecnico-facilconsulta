<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cidade extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'nome',
        'estado',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
