<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cliente::create([
            'id' => 1,
            'nome' => 'MTW',
            'street' => [
                "endereco" => "Rua joel reis, s/n",
                "bairro" => "centro",
                "cidade" => "Cardoso Moreira",
                "cep" => "28.180-000",
                "estado" => "Rio de Janeiro"
            ],
            "emails" => [
                "andre@mtw.com.br"
            ],
            "foto_brazao" => "",
            "maps" => "",
            "expediente" => "8h às 17h",
            "telefones" => [
                [
                    "tipo" => "telefone",
                    "numero" => "(22)8888-0987"
                ]
            ],
            "exibir_menu_vertical" => false
        ]);
    }
}
