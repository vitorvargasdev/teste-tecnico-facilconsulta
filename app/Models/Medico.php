<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medico extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'nome',
        'especialidade',
        'cidade_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function cidade(): BelongsTo
    {
        return $this->belongsTo(Cidade::class);
    }

    public function consultas(): HasMany
    {
        return $this->hasMany(Consulta::class);
    }
}
