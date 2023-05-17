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
use App\Models\Usuario;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

use Log;

class ClienteService
{
    use UtilsController;

    public function obtemTodos()
    {
        try {
            $dados = Cliente::where("id", ">", 0)->orderBy('id', 'desc')->get();
            foreach($dados as $d){
                $d->sistemas = ClienteService::obtemSistemasPorCliente($d->id);
            }

            return $dados;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::info($e);
            throw $e;
        }
    }

    public static function obtemSistemasPorCliente($cliente_id){
        $concessoes = Concessao::where('cliente_id', $cliente_id)->get();
        $sistemas=array();
        foreach($concessoes as $c){
            $sistema=Sistema::find($c->sistema_id);
            $sistema->modulos_ativos=$c->funcoes;
            array_push($sistemas, $sistema);
        }

        return $sistemas;
    }

    public function checaIdentificador($identificador, $id)
    {
        try {
            $dado = Cliente::where("nome", $identificador)->where("id", '!=', $id)->first();

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
            $dado = Cliente::where("id", $id)->first();

            return $dado;
        } catch (UnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function remove($id)
    {
        $setor = Cliente::find($id);

        if (!$setor) {
            return (new NotFoundException())->response();
        }

        $usuario = Usuario::where("cliente_id", $id)->count();
        if ($usuario > 0) {
            return [
                "erro" => "Problemas ao Deletar",
                "validacao" => "Existem usuários associados a este cliente",
                "code" => 402
            ];
        }

        $concessao = Concessao::where("cliente_id", $id)->count();
        if ($concessao > 0) {
            return [
                "erro" => "Problemas ao Deletar",
                "validacao" => "Existem concessões associadas a este cliente",
                "code" => 402
            ];
        }

        $d = unlink($setor->foto_brazao);

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Cliente",
            "tipo_operacao"  => "Exclusão",
            "descricao"      => "Exclusão Cliente, Cliente: " . $setor->id . ", Nome" . $setor->nome . " ",
            "ip"             => $this->get_client_ip(),
            "data_hora"      => Carbon::now(),
        ];

        AcoesUsuarioService::inserirAcao($array_log);
        $setor->delete();

        return array("code" => 200);
    }

    public function troca_brazao($req, $id)
    {
        $setor = Cliente::find($id);

        if (!is_link('storage')) {
            Artisan::call('storage:link');
        }

        if(\request()->hasFile('file')){
            if($setor->foto_brazao!=null){
                $d = unlink($setor->foto_brazao);
            }
            
            $nome = $req->file('file')->getClientOriginalName();
            $ext = explode('.', $nome);
            $ext = $ext[count($ext) - 1];
            $name = uniqid(date('HisYmd'));
            $name = "{$name}.{$ext}";
            $req->file('file')->storeAs('public/arquivos', $name);
            $setor->foto_brazao = storage_path('app/public/arquivos/' . $name);
        }
        $setor->save();

        return array(
            "code" => 200,
            "id" => $setor->id
        );
    }

    public function atualiza($req, $id)
    {
        $request=request()->all();

        $setor = Cliente::find($id);

        if (!$setor) {
            return (new NotFoundException())->response();
        }

        $setor->fill(request()->all());

        if (!is_link('storage')) {
            Artisan::call('storage:link');
        }

        if($request['remove_imagem']=='true'){
            try{
                if($setor->foto_brazao!=null){
                    $d = unlink($setor->foto_brazao);
                }
            }
            catch(\Exception $e){}
        }

        $setor->save();
        

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Cliente",
            "tipo_operacao"  => "Edição",
            "descricao"      => "Editar Cliente, Cliente: " . $setor->id . ", Nome" . $setor->nome . " ",
            "ip"             => $this->get_client_ip(),
            "data_hora"      => Carbon::now(),
        ];

        AcoesUsuarioService::inserirAcao($array_log);

        return array(
            "code" => 200,
            "id" => $setor->id
        );
    }

    public function adiciona($req)
    {
        $request=request()->all();

        $setor = new Cliente();
        $setor->fill(request()->all());

        if($request['remove_imagem']=='true'){
            try{
                if($setor->foto_brazao!=null){
                    $d = unlink($setor->foto_brazao);
                }
            }
            catch(\Exception $e){}
        }
        $setor->save();

        $array_log = [
            "id_usuario"     =>  Auth::id(),
            "tabela_afetada" => "Cliente",
            "tipo_operacao"  => "Adicionar",
            "descricao"      => "Adicionar Cliente, Cliente: " . $setor->id . ", Nome" . $setor->nome . " ",
            "ip"             => $this->get_client_ip(),
            "data_hora"      => Carbon::now(),
        ];

        AcoesUsuarioService::inserirAcao($array_log);

        return array(
            "code" => 200,
            "id" => $setor->id
        );
    }
}
