<?php

namespace App\Services;

use App\Exceptions\Forbidden;
use App\Exceptions\InternalServerError;
use App\Exceptions\NotFoundException;
use App\Exceptions\PayloadRequiredException;
use App\Exceptions\UnauthorizedException;
use App\Http\Controllers\UtilsController;
use App\Services\AcoesUsuarioService;


use App\Models\Concessao;
use App\Models\Sistema;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Log;

class ConcessaoService
{
    use UtilsController;

    public function obtemTodos()
    {
        try {
            $dados = Concessao::where("id", ">", 1)->orderBy('id', 'desc')->get();
            foreach($dados as $d){
                $d->sistema = Sistema::find($d->sistema_id);
                $d->sistema->qtd_modulos=sizeof($d->sistema->modulos);
                $d->cliente = Cliente::find($d->cliente_id);
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
            $dados = Concessao::where("cliente_id", $cliente)->orderBy('id', 'desc')->get();
            foreach($dados as $d){
                $d->sistema = Sistema::find($d->sistema_id);
                $d->sistema->qtd_modulos=sizeof($d->sistema->modulos);
                $d->cliente = Cliente::find($d->cliente_id);
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
            $dado = Concessao::where("id", $id)->first();
            $dado->sistema = Sistema::find($dado->permissao_id);
            $dado->cliente = Cliente::find($dado->cliente_id);

            return $dado;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function remove($id)
    {
        $setor = Concessao::find($id);

        if (!$setor) {
            return (new NotFoundException())->response();
        }

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Concessao",
            "tipo_operacao"  => "Exclusão",
            "descricao"      => "Exclusão Concessao, Concessao: " . $setor->id . ", Nome" . $setor->identificador . " ",
            "ip"             => $this->get_client_ip(),
            "data_hora"      => Carbon::now(),
        ];

        AcoesUsuarioService::inserirAcao($array_log);
        $setor->delete();

        return array("code" => 200);
    }

    public function atualiza($id)
    {
        $request = request()->all();

        $request['data_vencimento']=null;
        $temp=explode('/', $request['vencimento']);
        $data=$temp[2].'-'.$temp[1].'-'.$temp[0];

        $setor = Concessao::find($id);

        if (!$setor) {
            return (new NotFoundException())->response();
        }

        $setor->fill(request()->all());
        $setor->data_vencimento=$data;
        $setor->save();

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Concessao",
            "tipo_operacao"  => "Edição",
            "descricao"      => "Editar Concessao, Concessao: " . $setor->id . ", licenca: " . $setor->licenca . " ",
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

        $request = request()->all();

        $request['data_vencimento']=null;
        $temp=explode('/', $request['vencimento']);
        $data=$temp[2].'-'.$temp[1].'-'.$temp[0];

        $nivel = new Concessao();
        $nivel->fill(request()->all());
        $nivel->data_vencimento=$data;
        $nivel->save();

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Concessao",
            "tipo_operacao"  => "Adicionar",
            "descricao"      => "Adicionar Concessao, Concessao: " . $nivel->id . ", licenca: " . $nivel->licenca . " ",
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
