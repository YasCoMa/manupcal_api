<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habilitacao extends Model
{
    protected $fillable = [
        'usuario_id',
        'permissao_id',
        'sistema_id',
        'status'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'status' => "boolean"
    ];

    use HasFactory;
}
