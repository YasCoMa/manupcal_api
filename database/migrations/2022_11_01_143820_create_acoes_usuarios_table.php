<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acoes_usuarios', function (Blueprint $table) {
            $table->id();
            $table->integer('id_usuario');
            $table->string("tipo_operacao", 200);
            $table->string("tabela_afetada", 200);
            $table->string("descricao")->nullable();
            $table->dateTime("data_hora");
            $table->string('ip')->nullable();
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
        Schema::dropIfExists('acoes_usuarios');
    }
};
