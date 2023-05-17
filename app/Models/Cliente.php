<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static Config find(int $id)
 */
class Cliente extends Model
{

    use HasFactory;

    protected $fillable = [
        'nome',
        'street',
        'telefones',
        'emails',
        'facebook',
        'twitter',
        'instagram',
        'youtube',
        'api_chave_youtube',
        'id_playlist_youtube',
        'id_analytics',
        'foto_brazao',
        'modo',
        'maps',
        'expediente',
        'block_licitacao',
        'mostra_jornalista',
        'exibir_menu_vertical'
    ];

    protected $casts = [
        "mostra_jornalista" => "boolean",
        "block_licitacao" => "boolean",
        "exibir_menu_vertical" => "boolean",
        "street" => "json",
        "emails" => "json",
        "telefones" => "json"
    ];

}
