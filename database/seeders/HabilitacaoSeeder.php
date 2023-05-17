<?php

namespace Database\Seeders;

use App\Models\Habilitacao;
use Illuminate\Database\Seeder;

class HabilitacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Habilitacao::create([
            'id' => 1,
            'usuario_id' => 1,
            'permissao_id' => 1,
            'sistema_id' => 1,
            'status' => true
        ]);

        Habilitacao::create([
            'id' => 2,
            'usuario_id' => 1,
            'permissao_id' => 2,
            'sistema_id' => 2,
            'status' => true
        ]);
    }
}
