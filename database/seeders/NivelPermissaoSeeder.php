<?php

namespace Database\Seeders;

use App\Models\NivelPermissao;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NivelPermissaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NivelPermissao::create([
            'id' => 1,
            'sistema_id' => 1,
            'cliente_id' => 1,
            'identificador' => 'Super Administrador - sistema licenca',
            "modulos" => [ 
                "usuario" => true,
                "nivel_permissao" => true,
                "cliente" => true,
                "sistema_mtw" => true,
                "concessao" => true,
                "habilitacao" => true,
            ],
        ]);

        NivelPermissao::create([
            'id' => 2,
            'sistema_id' => 2,
            'cliente_id' => 1,
            'identificador' => 'Super Administrador - portalgov',
            "modulos" => [
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
        ]);

    }
}
