<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcoesUsuario extends Model
{
    use HasFactory;

    protected $fillable = [
        "id_usuario",
        "tipo_operacao",
        "tabela_afetada",
        "descricao",
        "data_hora",
        "ip"
    ];
}
