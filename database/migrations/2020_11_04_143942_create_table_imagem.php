<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableImagem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imagems', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_externo');
            $table->string("tipo", 300)->default("");
            $table->string('nome', 600)->unique();
            $table->string("legenda", 500)->nullable();
            $table->string("credito", 500)->nullable();
            $table->boolean("em_destaque")->default(false);
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
        Schema::dropIfExists('imagems');
    }
}
