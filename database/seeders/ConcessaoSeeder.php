<?php

namespace Database\Seeders;

use App\Models\Concessao;
use Illuminate\Database\Seeder;

class ConcessaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Concessao::create([
            'id' => 1,
            'cliente_id' => 1,
            'sistema_id' => 1,
            'licenca' => 'owner-s1',
            'data_vencimento' => '3000-01-01 23:59:59',
            'status' => true,
            "funcoes" => [
                "usuario" => true ,
                "nivel_permissao" => true  ,
                "cliente" => true ,
                "sistema_mtw" => true ,
                "concessao" => true ,
                "habilitacao" => true ,
            ]
        ]);

        Concessao::create([
            'id' => 2,
            'cliente_id' => 1,
            'sistema_id' => 2,
            'licenca' => 'owner-s2',
            'data_vencimento' => '3000-01-01 23:59:59',
            'status' => true,
            "funcoes" => [
                "menu"  => true,
                "noticia"  => true,
                "pagina"  => true,
                "legislacao"  => true,
                "licitacao"  => true,
                "aviso"  => true,
                "banner"  => true,
                "utilidade"  => true,
                "concurso"  => true,
                "processo_seletivo"  => true,
                "pergunta"  => true,
                "rel_lrf"  => true,
                "telefone_util"  => true,
                "ponto"  => true,
                "cultura"  => true,
                "secretaria"  => true,
                "escola"  => true,
                "hospedagem"  => true,
                "restaurante"  => true,
                "ponto_taxi"  => true,
                "transporte_publico"  => true,
                "farmacia"  => true,
                "rel_transparencia"  => true,
                "diario_oficial"  => true,
                "galeria"  => true,
                "evento"  => true,
                "conselho"  => true,
                "projeto"  => true,
                "convenio"  => true,
                "carta_servico"  => true,
                "prestador_servico"  => true,
                "vereador"  => true,
                "propositura"  => true,
                "pauta"  => true,
                "legislatura"  => true
            ],
            "funcoes_publicas" => [
                "permissao"  => false,
                "usuario"  => false,
                "menu"  => true,
                "noticia"  => true,
                "pagina"  => true,
                "legislacao"  => true,
                "licitacao"  => true,
                "aviso"  => true,
                "banner"  => true,
                "utilidade"  => true,
                "concurso"  => true,
                "processo_seletivo"  => true,
                "pergunta"  => true,
                "rel_lrf"  => true,
                "telefone_util"  => true,
                "ponto"  => true,
                "cultura"  => true,
                "secretaria"  => true,
                "escola"  => true,
                "hospedagem"  => true,
                "restaurante"  => true,
                "ponto_taxi"  => true,
                "transporte_publico"  => true,
                "farmacia"  => true,
                "rel_transparencia"  => true,
                "diario_oficial"  => true,
                "galeria"  => true,
                "evento"  => true,
                "conselho"  => true,
                "projeto"  => true,
                "convenio"  => true,
                "carta_servico"  => true,
                "prestador_servico"  => true,
                "vereador"  => true,
                "propositura"  => true,
                "pauta"  => true,
                "legislatura"  => true
            ],
            "funcoes_com_categorizacao" => [
                "aviso" => true,
                "pergunta" => true,
                "diario_oficial" => true,
                "cultura" => true,
                "rel_transparencia" => true,
                "utilidade" => true,
                "telefone_util" => true,
                "propositura" => true,
                "noticia"  => true,
                "pagina"  => true,
                "processo_seletivo"  => true,
                "legislatura" => true,
                "arquivos" => true,
                "concurso" => true,
                "licitacao" => true,
                "legislacao" => true,
                "secretaria" => true,
                "item_pauta" => true,
                "banner" => true,
                "pauta" => true,
                "menu" => true,
                "unidade" => true,
                "conselho" => true,
                "escola" => true,
                "post" => true,
                "ponto_turistico" => true,
                "restaurante" => true,
                "projeto" => true,
                "galeria" => true,
                "hospedagem" => true,
                "funcionario" => true,
                "evento" => true,
                "rel_lrf" => true,
                "vereador" => true
            ]
        ]);
    }
}
