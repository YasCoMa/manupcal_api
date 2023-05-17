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
            'nome' => 'SoftSys',
            'street' => [
                "endereco" => "Rua alice maria, s/n",
                "bairro" => "Centro",
                "cidade" => "Ouro Negro",
                "cep" => "22.222-000",
                "estado" => "Rio de Janeiro"
            ],
            "emails" => [
                "joao@softsys.com.br"
            ],
            "foto_brazao" => "",
            "maps" => "",
            "expediente" => "8h às 17h",
            "telefones" => [
                [
                    "tipo" => "telefone",
                    "numero" => "(24) 98888-9999"
                ]
            ],
            "exibir_menu_vertical" => false
        ]);
    }
}
