<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\NivelPermissaoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\SistemaController;
use App\Http\Controllers\ConcessaoController;
use App\Http\Controllers\HabilitacaoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('criptografado', function () {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    // generate a pin based on 2 * 7 digits + a random character
    $pin = mt_rand(1000000, 9999999)
        . mt_rand(1000000, 9999999)
        . $characters[rand(0, strlen($characters) - 1)];

    // shuffle the result
    $string = str_shuffle($pin);

    $key = "program@doresMtwTudoSinistros3000".$string;
    $key = Crypt::encrypt($key);
    return [
        "code" => 200,
        "key" => $key,
        //"key" => md5($key),
    ];
});

Route::resource("usuarios", UsuarioController::class)->except([
    'create', 'edit'
]);
Route::get('usuarios/sistema/{sistema_id}', [UsuarioController::class, 'index_sistema']);
Route::get('usuarios/sistema/{sistema_id}/{usuario_id}', [UsuarioController::class, 'show_sistema']);

Route::post('autenticacao', [UsuarioController::class, 'authenticate']);
Route::get('valida-login/{login}/{id}', [UsuarioController::class, 'checaLogin']);
Route::get('valida-email/{email}/{id}', [UsuarioController::class, 'checaEmail']);

Route::resource("permissoes", NivelPermissaoController::class)->except([
    'create', 'edit',
]);
Route::get('permissoes/cliente/{cliente_id}', [NivelPermissaoController::class, 'index_cliente']);
Route::get('permissoes/cliente/{cliente_id}/{permissao_id}', [NivelPermissaoController::class, 'show_cliente']);


Route::get('criptografado', function () {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    // generate a pin based on 2 * 7 digits + a random character
    $pin = mt_rand(1000000, 9999999)
        . mt_rand(1000000, 9999999)
        . $characters[rand(0, strlen($characters) - 1)];

    // shuffle the result
    $string = str_shuffle($pin);

    $key = "program@doresMtwTudoSinistros3000".$string;
    $key = Crypt::encrypt($key);
    return [
        "code" => 200,
        "key" => $key,
        //"key" => md5($key),
    ];
});

Route::get('valida-identificador/{identificador}/{id}/{sistema}/{cliente}', [NivelPermissaoController::class, 'checaIdentificador']);
Route::get('permissoes-por-sistema/{id}', [NivelPermissaoController::class, 'listaPorSistema']);

Route::resource("clientes", ClienteController::class)->except([
    'create', 'edit',
]);
Route::post('clientes/imagem/{id}', [ClienteController::class, 'update_brazao']);
Route::get('valida-nome-cliente/{identificador}/{id}', [ClienteController::class, 'checaIdentificador']);
Route::get('usuarios-por-cliente/{id}', [ClienteController::class, 'listaPorCliente']);

Route::resource("sistemas", SistemaController::class)->except([
    'create', 'edit',
]);
Route::get('valida-nome-sistema/{identificador}/{id}', [SistemaController::class, 'checaIdentificador']);
Route::get('sistemas/cliente/{cliente}', [SistemaController::class, 'index_cliente']);

Route::resource("concessoes", ConcessaoController::class)->except([
    'create', 'edit',
]);
Route::get('concessoes/cliente/{cliente}', [ConcessaoController::class, 'index_cliente']);

Route::resource("habilitacoes", HabilitacaoController::class)->except([
    'create', 'edit',
]);
Route::get('habilitacoes/usuario/{usuario}', [HabilitacaoController::class, 'index_usuario']);
