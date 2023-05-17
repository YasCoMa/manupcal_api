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
        Schema::create('nivel_permissaos', function (Blueprint $table) {
            $table->id();
            $table->string("identificador")->unique();
            $table->json('modulos');
            $table->integer("sistema_id")->default(0);
            $table->integer("cliente_id")->default(0);
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
        Schema::dropIfExists('nivel_permissaos');
    }
};
