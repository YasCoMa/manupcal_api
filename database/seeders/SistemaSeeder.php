<?php

namespace Database\Seeders;

use App\Models\Sistema;
use Illuminate\Database\Seeder;

class SistemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sistema::create([
            'id' => 1,
            'nome' => 'Controle de Licenças',
            "modulos" => [
                [ "nome" => "Usuário", "etiqueta" => "usuario" ],
                [ "nome" => "Nível de Permissão", "etiqueta" => "nivel_permissao" ],
                [ "nome" => "Cliente", "etiqueta" => "cliente" ],
                [ "nome" => "Sistemas Web", "etiqueta" => "sistemas_web" ],
                [ "nome" => "Concessão", "etiqueta" => "concessao" ],
                [ "nome" => "Habilitação", "etiqueta" => "habilitacao" ],
            ],
        ]);

        Sistema::create([
            'id' => 2,
            'nome' => 'Sistema de Gerenciamento de conteúdo',
            "modulos" => [
                [ "nome" => "Menu", "etiqueta" => "menu"  ],
                [ "nome" => "Notícia", "etiqueta" => "noticia"  ],
                [ "nome" => "Página", "etiqueta" => "pagina"  ],
                [ "nome" => "Legislação", "etiqueta" => "legislacao"  ],
                [ "nome" => "Licitação", "etiqueta" => "licitacao"  ],
                [ "nome" => "Aviso", "etiqueta" => "aviso"  ],
                [ "nome" => "Banner", "etiqueta" => "banner"  ],
                [ "nome" => "Utilidade", "etiqueta" => "utilidade"  ],
                [ "nome" => "Concurso", "etiqueta" => "concurso"  ],
                [ "nome" => "Processo Seletivo", "etiqueta" => "processo_seletivo"  ],
                [ "nome" => "Pergunta Frequente", "etiqueta" => "pergunta"  ],
                [ "nome" => "Relatório de Responsabilidade Fiscal", "etiqueta" => "rel_lrf"  ],
                [ "nome" => "Telefone Útil", "etiqueta" => "telefone_util"  ],
                [ "nome" => "Ponto Turístico", "etiqueta" => "ponto"  ],
                [ "nome" => "Centro Cultural", "etiqueta" => "cultura"  ],
                [ "nome" => "Secretaria", "etiqueta" => "secretaria"  ],
                [ "nome" => "Escola", "etiqueta" => "escola"  ],
                [ "nome" => "Hospedagem", "etiqueta" => "hospedagem"  ],
                [ "nome" => "Restaurante", "etiqueta" => "restaurante"  ],
                [ "nome" => "Ponto de Táxi", "etiqueta" => "ponto_taxi"  ],
                [ "nome" => "Transporte Público", "etiqueta" => "transporte_publico"  ],
                [ "nome" => "Farmácia", "etiqueta" => "farmacia"  ],
                [ "nome" => "Relatório de Transparência", "etiqueta" => "rel_transparencia"  ],
                [ "nome" => "Diário Oficial", "etiqueta" => "diario_oficial"  ],
                [ "nome" => "Galeria de Imagens", "etiqueta" => "galeria"  ],
                [ "nome" => "Evento", "etiqueta" => "evento"  ],
                [ "nome" => "Conselho", "etiqueta" => "conselho"  ],
                [ "nome" => "Projeto", "etiqueta" => "projeto"  ],
                [ "nome" => "Convênio", "etiqueta" => "convenio"  ],
                [ "nome" => "Carta de Serviço", "etiqueta" => "carta_servico"  ],
                [ "nome" => "Prestador de Serviço", "etiqueta" => "prestador_servico"  ],
                [ "nome" => "Vereador", "etiqueta" => "vereador"  ],
                [ "nome" => "Propositura", "etiqueta" => "propositura"  ],
                [ "nome" => "Pauta", "etiqueta" => "pauta"  ],
                [ "nome" => "Legislatura", "etiqueta" => "legislatura" ]
            ],
        ]);
    }
}
