<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelPermissao extends Model
{
    protected $fillable = [
        'identificador',
        'cliente_id',
        'sistema_id',
        'modulos',
        'status'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'modulos' => "json",
        'status' => "boolean"
    ];

    use HasFactory;
}
