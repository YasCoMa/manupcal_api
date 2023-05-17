<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Usuario::create([
            'id' => 1,
            'nome' => 'suporte',
            'sobrenome' => 'ypu-yas',
            'email' => "yasmmin@ypublish.info",
            'login' => 'yasmmin',
            'senha' => md5('yasmmin'),
            'status' => true,
            'cliente_id' => 1
        ]);
        Usuario::create([
            'id' => 2,
            'nome' => 'suporte',
            'sobrenome' => 'ypu',
            'email' => "suporte@ypublish.info",
            'login' => 'suporte',
            'senha' => md5('@admin'),
            'status' => true,
            'cliente_id' => 1
        ]);
    }
}
