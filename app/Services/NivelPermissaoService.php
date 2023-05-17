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

use App\Models\Cliente;
use App\Models\Sistema;
use App\Models\Habilitacao;
use App\Models\Concessao;
use App\Models\Usuario;
use App\Models\NivelPermissao;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Log;

class NivelPermissaoService
{
    use UtilsController;

    public function obtemTodos()
    {
        try {
            $dados = NivelPermissao::where("id", ">", 1)->orderBy('id', 'desc')->get();
            foreach($dados as $d){
                $d->sistema = Sistema::find($d->sistema_id);
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

    public function obtemTodosCliente($cliente)
    {
        try {
            $resultado=array();
            $dados = NivelPermissao::where("id", ">", 1)->orderBy('id', 'desc')->get();
            foreach($dados as $d){
                $d->sistema = Sistema::find($d->sistema_id);
                $d->cliente = Cliente::find($d->cliente_id);
                
                // Checa se o sistema da permissão foi concedido ao 
                $concessao = Concessao::where('cliente_id', $cliente)->where('sistema_id', $d->sistema_id)->count();
                if($concessao>0){
                    array_push($resultado, $d);
                }
            }
            return $resultado;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::info($e);
            throw $e;
        }
    }

    public function checaIdentificador($identificador, $id, $sistema, $cliente)
    {
        try {
            $dado = NivelPermissao::where("identificador", $identificador)->where("sistema_id", $sistema)->where("cliente_id", $cliente)->where("id", '!=', $id)->first();

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
            $dado = NivelPermissao::where("id", $id)->first();
            $dado->sistema = Sistema::find($dado->sistema_id);
            $dado->cliente = Cliente::find($dado->cliente_id);
            
            return $dado;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function obtemDadosCliente($cliente, $id)
    {
        try {
            $dado = NivelPermissao::where("id", $id)->first();
            $dado->sistema = Sistema::find($dado->sistema_id);
            $dado->cliente = Cliente::find($dado->cliente_id);
            $concessao = Concessao::where('cliente_id', $cliente)->where('sistema_id', $dado->sistema_id)->first();
            $dado->sistema->modulos_ativos=$concessao->funcoes;

            $dado->cliente->sistemas= ClienteService::obtemSistemasPorCliente($dado->cliente->id);

            return $dado;
        } 
        catch (UnauthorizedException $e) {
            throw $e;
        } 
        catch (\Exception $e) {
            Log::info($e->getMessage());
            throw $e;
        }
    }

    public function obtemDadosPorSistema($id)
    {
        try {
            $dados = NivelPermissao::where("sistema_id", $id)->get();
            foreach($dados as $d){
                $d->sistema = Sistema::find($d->sistema_id);
                $d->cliente = Cliente::find($d->cliente_id);
            }
            return $dados;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function remove($id)
    {
        $setor = NivelPermissao::find($id);

        if (!$setor) {
            return (new NotFoundException())->response();
        }

        $usuario = Habilitacao::where("permissao_id", $id)->count();
        if ($usuario > 0) {
            return [
                "erro" => "Problemas ao Deletar",
                "validacao" => "Existem usuários utilizando esta permissão",
                "code" => 402
            ];
        }

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Nivel Permissão",
            "tipo_operacao"  => "Exclusão",
            "descricao"      => "Exclusão Nivel Permissão, Nivel Permissão: " . $setor->id . ", Nome" . $setor->identificador . " ",
            "ip"             => $this->get_client_ip(),
            "data_hora"      => Carbon::now(),
        ];

        AcoesUsuarioService::inserirAcao($array_log);
        $setor->delete();

        /*$modulo="Nível de permissão";
        $idLogged = Auth::id();
        AcoesUsuario::create([
            'id_usuario' => $idLogged,
            'tipo_operacao' => 'Exclusão',
            'tabela_afetada' => $modulo,
            'data_hora' => \Carbon\Carbon::now(),
            'descricao' => 'Nome: '.($setor->identificador).' <br />IP de Acesso: '.($this->get_client_ip())
        ]);*/

        return array("code" => 200);
    }

    public function atualiza($id)
    {
        $setor = NivelPermissao::find($id);

        if (!$setor) {
            return (new NotFoundException())->response();
        }

        $setor->fill(request()->all());
        $setor->save();

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Nivel Permissão",
            "tipo_operacao"  => "Edição",
            "descricao"      => "Editar Nivel Permissão, Nivel Permissão: " . $setor->id . ", Nome" . $setor->identificador . " ",
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

        $nivel = new NivelPermissao();
        $nivel->fill(request()->all());
        $nivel->save();

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Nivel Permissão",
            "tipo_operacao"  => "Adicionar",
            "descricao"      => "Adicionar Nivel Permissão, Nivel Permissão: " . $nivel->id . ", Nome" . $nivel->identificador . " ",
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
