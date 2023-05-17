<?php

namespace App\Services;

use App\Exceptions\Forbidden;
use App\Exceptions\InternalServerError;
use App\Exceptions\NotFoundException;
use App\Exceptions\PayloadRequiredException;
use App\Exceptions\UnauthorizedException;

use Illuminate\Support\Facades\Auth;

use App\Services\AcoesUsuarioService;
use App\Services\ClienteService;

use App\Http\Controllers\UtilsController;

use App\Models\Cliente;
use App\Models\NivelPermissao;
use App\Models\Habilitacao;
use App\Models\Usuario;

use Carbon\Carbon;


use Illuminate\Support\Facades\DB;

use Log;

class UsuarioService
{
    use UtilsController;


    public function autenticaUsuario($login, $senha, $sistema)
    {
        try {
            $usuario = Usuario::where("senha", md5($senha))
                ->where(function ($query) use ($login) {
                    $query->where("login", $login)
                        ->orWhere("email", $login);
                })->first();

            if (!$usuario) {
                throw new UnauthorizedException();
            }
            $hab=Habilitacao::where("usuario_id", $usuario->id)->where("sistema_id", $sistema)->first();
            $usuario->permissao = NivelPermissao::find($hab->permissao_id);
            $usuario->cliente = Cliente::find($usuario->cliente_id);
            return $usuario;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function obtemTodos()
    {
        try {
            $usuario = auth()->user();
            $usuarios = Usuario::where("id", ">", 0)->orderBy('id', 'desc')->get();

            foreach($usuarios as $u){
                $u->cliente = Cliente::find($u->cliente_id);
            }

            return $usuarios;
        } 
        catch (UnauthorizedException $e) {
            throw $e;
        } 
        catch (\Exception $e) {
            throw $e;
        }
    }

    public function obtemTodosSistema($sistema_id)
    {
        try {
            $usuario = auth()->user();
            $usuarios = Usuario::where("id", ">", 0)->orderBy('id', 'desc')->get();

            foreach($usuarios as $u){
                $u->cliente = Cliente::find($u->cliente_id);

                //$hab=Habilitacao::where("usuario_id", $u->id)->where("sistema_id", $sistema_id)->first();
                //$u->permissao = NivelPermissao::find($hab->permissao_id);
            }

            return $usuarios;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function obtemDadosUsuario($id)
    {
        try {

            $usuario = Usuario::where("id", $id)->first();
            $usuario->cliente = Cliente::find($usuario->cliente_id);
            $usuario->cliente->sistemas = ClienteService::obtemSistemasPorCliente($usuario->cliente->id);

            return $usuario;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function obtemDadosUsuarioSistema($sistema_id, $id)
    {
        try {

            $usuario = Usuario::where("id", $id)->first();

            return $usuario;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function obtemDadosPorCliente($id)
    {
        try {
            $dados = Usuario::where("cliente_id", $id)->get();
            
            return $dados;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function checaExistenciaLogin($login, $id)
    {
        try {
            $usuario = Usuario::where("login", $login)->where("id", '!=', $id)->first();

            return $usuario;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function checaExistenciaEmail($email, $id)
    {
        try {
            $usuario = Usuario::where("email", $email)->where("id", '!=', $id)->first();

            return $usuario;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function remove($id)
    {
        $usuario = Usuario::where("id", $id)->first();
        $titulo = $usuario->nome;

        if (!$usuario) {
            return (new NotFoundException())->response();
        }

        $idLogged = Auth::id();
        if ($idLogged == $usuario->id) {
            return (new Forbidden())
                ->setExtra("action", "usuario_logado")
                ->response();
        }

        $msg = Habilitacao::where("usuario_id", $id)->count();
        if ($msg > 0) {
            return [
                "erro" => "Problemas ao Deletar",
                "validacao" => "Existem habilitações de permissão para este usuário",
                "code" => 402
            ];
        }

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Usuario",
            "tipo_operacao"  => "Exclusão",
            "descricao"      => "Excluir Usuário, Usuário: " . $usuario->id . ", Nome" . $usuario->nome . " ",
            "ip"             => $this->get_client_ip(),
            "data_hora"      => Carbon::now(),
        ];

        AcoesUsuarioService::inserirAcao($array_log);

        $usuario->delete();

        return array(
            "code" => 200,
            "id" => $id
        );
    }

    public function atualiza($request, $id)
    {
        $count =  Usuario::where('email', '=', $request['email'])->where('id', '!=', $request['id'])->count();
        if ($count > 0) {
            return (new PayloadRequiredException())
                ->setExtra("mensagem", "Já existe um usuário com o mesmo e-mail!")
                ->response();
        }

        $usuario = Usuario::where("id", $id)->first();
        if (!$usuario) {
            return (new NotFoundException())->response();
        }

        if (isset($request['senha'])) {
            if (isset($request->senha) || strlen(trim($request['senha'])) > 0 || trim($request['senha']) != '') {
                $usuario->senha = md5($request['senha']);
            } else {
                unset($request['senha']);
            }
        }

        if (isset($request['cliente_id'])) {
            $usuario->cliente_id = (int) $request['cliente_id'];
        }

        if (isset($request['login'])) {
            $usuario->login = $request['login'];
        }

        if (isset($request['email'])) {
            $usuario->email = $request['email'];
        }

        if (isset($request['sobrenome'])) {
            $usuario->sobrenome = $request['sobrenome'];
        }

        if (isset($request['nome'])) {
            $usuario->nome = $request['nome'];
        }
        $usuario->status = (bool) $request['status'];

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Usuario",
            "tipo_operacao"  => "Edição",
            "descricao"      => "Editar Usuário, Usuário: " . $usuario->id . ", Nome" . $usuario->nome . " ",
            "ip"             => $this->get_client_ip(),
            "data_hora"      => Carbon::now(),
        ];

        AcoesUsuarioService::inserirAcao($array_log);
        $usuario->save();

        return array(
            "code" => 200,
            "id" => $id
        );
    }

    public function adiciona($request)
    {
        try {
            $count =  Usuario::where('email', '=', $request['email'])->where('id', '!=', $request['id'])->count();
            if ($count > 0) {
                return (new PayloadRequiredException())
                    ->setExtra("mensagem", "Já existe um usuário com o mesmo e-mail!")
                    ->response();
            }

            $request['senha'] = md5($request['senha']);

            $setor = new Usuario();
            //$setor->fill($request);
            $setor->nome = $request['nome'];
            $setor->sobrenome = $request['sobrenome'];
            $setor->email = $request['email'];
            $setor->cliente_id = $request['cliente_id'];
            $setor->senha = $request['senha'];
            $setor->login = $request['login'];
            $setor->status = true;
            $setor->save();

            $array_log = [
                "id_usuario"     =>  Auth::id(),
                "tabela_afetada" => "Usuario",
                "tipo_operacao"  => "Adicionar",
                "descricao"      => "Adicionar Usuário, Usuário: " . $setor->id . ", Nome" . $setor->nome . " ",
                "ip"             => $this->get_client_ip(),
                "data_hora"      => Carbon::now(),
            ];

            AcoesUsuarioService::inserirAcao($array_log);

            /*$modulo="Usuário";
            $idLogged = Auth::id();
            AcoesUsuario::create([
                'id_usuario' => $idLogged,
                'tipo_operacao' => 'Inserção',
                'tabela_afetada' => $modulo,
                'data_hora' => \Carbon\Carbon::now(),
                'descricao' => 'Nome: '.($setor->nome).' <br />IP de Acesso: '.($this->get_client_ip())
            ]);*/

            return array(
                "code" => 200,
                "id" => $setor->id
            );
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return \response([
                'status' => 500,
                //'erro' =>$e->getMessage()
            ], 500);
        }
    }
}
