<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsuarioSeeder::class);
        $this->call(NivelPermissaoSeeder::class);
        $this->call(ClienteSeeder::class);
        $this->call(SistemaSeeder::class);
        $this->call(ConcessaoSeeder::class);
        $this->call(HabilitacaoSeeder::class);

    }
}
