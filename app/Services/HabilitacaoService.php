<?php

namespace App\Services;

use App\Exceptions\Forbidden;
use App\Exceptions\InternalServerError;
use App\Exceptions\NotFoundException;
use App\Exceptions\PayloadRequiredException;
use App\Exceptions\UnauthorizedException;
use App\Http\Controllers\UtilsController;

use App\Services\AcoesUsuarioService;


use App\Models\Habilitacao;
use App\Models\Sistema;
use App\Models\Usuario;
use App\Models\NivelPermissao;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Log;

class HabilitacaoService
{
    use UtilsController;

    public function obtemTodos()
    {
        try {
            $dados = Habilitacao::where("id", ">", 0)->orderBy('id', 'desc')->get();
            foreach($dados as $d){
                $d->usuario = Usuario::find($d->usuario_id);
                $d->permissao = NivelPermissao::find($d->permissao_id);
                $d->sistema = Sistema::find($d->sistema_id);
                $d->sistema->permissoes = NivelPermissao::where('sistema_id', $d->id)->get();
            }
            return $dados;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::info($e);
            throw $e;
        }
    }

    public function obtemTodosPorUsuario($usuario)
    {
        try {
            $dados = Habilitacao::where("usuario_id", $usuario)->orderBy('id', 'desc')->get();
            foreach($dados as $d){
                $d->permissao = NivelPermissao::select('id', 'identificador')->where('id', $d->permissao_id)->first();
                $d->sistema = Sistema::find($d->sistema_id);
                $d->sistema->permissoes = NivelPermissao::select('id', 'identificador')->where('sistema_id', $d->sistema_id)->get();
            }
            return $dados;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::info($e);
            throw $e;
        }
    }

    public function obtemDados($id)
    {
        try {
            $dado = Habilitacao::where("id", $id)->first();
            $dado->permissao = NivelPermissao::select('id', 'identificador')->where('id', $dado->permissao_id)->first();
            $dado->sistema = Sistema::find($dado->sistema_id);
            $dado->sistema->permissoes = NivelPermissao::select('id', 'identificador')->where('sistema_id', $dado->sistema_id)->get();

            return $dado;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function remove($id)
    {
        $setor = Habilitacao::find($id);

        if (!$setor) {
            return (new NotFoundException())->response();
        }

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Habilitacao",
            "tipo_operacao"  => "Exclusão",
            "descricao"      => "Exclusão Habilitacao, Habilitacao: " . $setor->id . ", Nome" . $setor->identificador . " ",
            "ip"             => $this->get_client_ip(),
            "data_hora"      => Carbon::now(),
        ];

        AcoesUsuarioService::inserirAcao($array_log);
        $setor->delete();

        return array("code" => 200);
    }

    public function atualiza($id)
    {
        $dado = Habilitacao::find($id);

        if (!$dado) {
            return (new NotFoundException())->response();
        }

        $dado->fill(request()->all());
        $dado->save();

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Habilitacao",
            "tipo_operacao"  => "Edição",
            "descricao"      => "Editar Habilitacao, Habilitacao: " . $setor->id . ", Sistema: " . $dado->sistema_id . ", Permissao: " . $dado->permissao_id . ", Usuário: " . $dado->usuario_id . " ",
            "ip"             => $this->get_client_ip(),
            "data_hora"      => Carbon::now(),
        ];

        AcoesUsuarioService::inserirAcao($array_log);

        return array(
            "code" => 200,
            "id" => $setor->id,
            "objeto" => $setor
        );
    }

    public function adiciona()
    {

        $dado = new Habilitacao();
        $dado->fill(request()->all());
        $dado->save();

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Habilitacao",
            "tipo_operacao"  => "Adicionar",
            "descricao"      => "Adicionar Habilitacao, Habilitacao: " . $dado->id . ", Sistema: " . $dado->sistema_id . ", Permissao: " . $dado->permissao_id . ", Usuário: " . $dado->usuario_id . " ",
            "ip"             => $this->get_client_ip(),
            "data_hora"      => Carbon::now(),
        ];

        AcoesUsuarioService::inserirAcao($array_log);

        return array(
            "code" => 200,
            "id" => $dado->id,
            "objeto" => $dado
        );
    }
}
