<?php

namespace App\Services;

use App\Exceptions\Forbidden;
use App\Exceptions\InternalServerError;
use App\Exceptions\NotFoundException;
use App\Exceptions\PayloadRequiredException;
use App\Exceptions\UnauthorizedException;
use App\Http\Controllers\UtilsController;

use App\Services\AcoesUsuarioService;
use App\Services\ClienteService;

use App\Models\Sistema;
use App\Models\NivelPermissao;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Log;

class SistemaService
{
    use UtilsController;

    public function obtemTodos()
    {
        try {
            $dados = Sistema::where("id", ">", 0)->orderBy('id', 'desc')->get();
            foreach($dados as $d){
                $d->qtd_modulos=sizeof($d->modulos);
            }
            
            return $dados;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::info($e);
            throw $e;
        }
    }

    public function obtemTodosPorCliente($cliente)
    {
        try {
            $dados = ClienteService::obtemSistemasPorCliente($cliente);
            foreach($dados as $d){
                $d->permissoes = NivelPermissao::select('id', 'identificador')->where('sistema_id', $d->id)->get();
                unset($d->modulos_ativos);
            }
            return $dados;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::info($e);
            throw $e;
        }
    }

    public function checaIdentificador($identificador, $id)
    {
        try {
            $dado = Sistema::where("nome", $identificador)->where("id", '!=', $id)->first();

            return $dado;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function obtemDados($id)
    {
        try {
            $dado = Sistema::where("id", $id)->first();
            $dado->permissao = NivelPermissao::find($dado->permissao_id);

            return $dado;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function remove($id)
    {
        $setor = Sistema::find($id);

        if (!$setor) {
            return (new NotFoundException())->response();
        }

        $usuario = NivelPermissao::where("sistema_id", $id)->count();
        if ($usuario > 0) {
            return [
                "erro" => "Problemas ao Deletar",
                "validacao" => "Existem usuários utilizando esta permissão",
                "code" => 402
            ];
        }

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Sistema",
            "tipo_operacao"  => "Exclusão",
            "descricao"      => "Exclusão Sistema, Sistema: " . $setor->id . ", Nome" . $setor->identificador . " ",
            "ip"             => $this->get_client_ip(),
            "data_hora"      => Carbon::now(),
        ];

        AcoesUsuarioService::inserirAcao($array_log);
        $setor->delete();

        return array("code" => 200);
    }

    public function atualiza($id)
    {
        $setor = Sistema::find($id);

        if (!$setor) {
            return (new NotFoundException())->response();
        }

        $setor->fill(request()->all());
        $setor->save();

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Sistema",
            "tipo_operacao"  => "Edição",
            "descricao"      => "Editar Sistema, Sistema: " . $setor->id . ", Nome" . $setor->identificador . " ",
            "ip"             => $this->get_client_ip(),
            "data_hora"      => Carbon::now(),
        ];

        AcoesUsuarioService::inserirAcao($array_log);

        return array(
            "code" => 200,
            "id" => $setor->id
        );
    }

    public function adiciona()
    {

        $nivel = new Sistema();
        $nivel->fill(request()->all());
        $nivel->save();

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Sistema",
            "tipo_operacao"  => "Adicionar",
            "descricao"      => "Adicionar Sistema, Sistema: " . $nivel->id . ", Nome" . $nivel->identificador . " ",
            "ip"             => $this->get_client_ip(),
            "data_hora"      => Carbon::now(),
        ];

        AcoesUsuarioService::inserirAcao($array_log);

        return array(
            "code" => 200,
            "id" => $nivel->id
        );
    }
}
