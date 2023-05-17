<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCliente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome', 160)->unique();
            $table->string('facebook', 400)->nullable();
            $table->string('twitter', 400)->nullable();
            $table->string('instagram', 400)->nullable();
            $table->string('youtube', 400)->nullable();
            $table->string('api_chave_youtube', 400)->nullable();
            $table->string('id_playlist_youtube', 400)->nullable();
            $table->string('id_analytics', 400)->nullable();
            $table->json('street');
            $table->json("telefones");
            $table->json("emails");
            $table->string("foto_brazao", 250)->default("")->nullable();
            $table->string("modo")->default("Prefeitura");
            $table->text("maps")->nullable();
            $table->boolean("block_licitacao")->default(false);
            $table->string("expediente")->default("")->nullable();
            $table->boolean("mostra_jornalista")->default(false);
            $table->boolean("exibir_menu_vertical")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes');
    }
}
