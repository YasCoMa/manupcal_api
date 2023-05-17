<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\AcoesUsuario;

class AcoesUsuarioService
{
    /*
    * Criar Log
    */
    public static function inserirAcao($arr)
	{
        try {
            $funcao = new AcoesUsuario();
            $funcao->id_usuario = $arr['id_usuario'];
            $funcao->tabela_afetada = $arr['tabela_afetada'];
            $funcao->tipo_operacao = $arr['tipo_operacao'];
            $funcao->descricao = $arr['descricao'];
            $funcao->data_hora = $arr["data_hora"];
            $funcao->ip = $arr["ip"];
            $funcao->save();
        }catch (\Exception $e) {
            return \response([
                'status' => 500,
                'erro' =>$e->getMessage()
            ],500 );
        }
    }
}
