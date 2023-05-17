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
        Schema::create('concessaos', function (Blueprint $table) {
            $table->id();
            $table->string("licenca")->unique();
            $table->dateTime("data_vencimento")->nullable();
            $table->json("funcoes");
            $table->json("funcoes_publicas")->nullable();
            $table->json("funcoes_com_categorizacao")->nullable();
            $table->integer("cliente_id")->default(0);
            $table->integer("sistema_id")->default(0);
            $table->boolean("status")->default(false);
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
        Schema::dropIfExists('concessaos');
    }
};
