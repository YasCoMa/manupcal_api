<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concessao extends Model
{
    protected $fillable = [
        'licenca',
        'data_vencimento',
        'funcoes',
        'funcoes_publicas',
        'funcoes_com_categorizacao',
        'cliente_id',
        'sistema_id',
        'status'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'funcoes' => "json",
        'funcoes_publicas' => "json",
        'funcoes_com_categorizacao' => "json",
        'status' => "boolean"
    ];

    protected $appends = ['vencimento'];

    public function getVencimentoAttribute() {  
        $date = new \DateTime($this->data_envio);
        return $date->format('d/m/Y H:i:s');
    }

    use HasFactory;
}
