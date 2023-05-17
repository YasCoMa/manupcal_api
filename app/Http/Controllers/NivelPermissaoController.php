<?php

namespace App\Http\Controllers;

use App\Services\NivelPermissaoService;

use App\Exceptions\Forbidden;
use App\Exceptions\InternalServerError;
use App\Exceptions\NotFoundException;
use App\Exceptions\PayloadRequiredException;
use App\Exceptions\UnauthorizedException;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Requests\AuthenticateRequest;
use Illuminate\Http\JsonResponse;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Crypt;
class NivelPermissaoController extends Controller
{
    private $service;

    function __construct(NivelPermissaoService $service)
    {
        $this->permissaoService = $service;
        $this->middleware('jwt.auth');
    }

    use UtilsController;

     /**
     * @api {get} /permissoes Lista Permissoes
     * @apiName ListaPermissao
     * @apiGroup Nivel Permissao
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function index () {
        try {
            $dados = $this->permissaoService->obtemTodos();

            return [
                "code" => 200,
                "payload" => $dados
            ];
        } catch (UnauthorizedException $e) {
            return (new UnauthorizedException())->response();
        } catch (\Exception $e) {
            return (new InternalServerError())->response();
        }
    }

    /**
     * @api {get} /permissoes/cliente/{cliente_id} Lista Permissoes
     * @apiName ListaPermissao
     * @apiGroup Nivel Permissao
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function index_cliente ($cliente) {
        try {
            $dados = $this->permissaoService->obtemTodosCliente($cliente);

            return [
                "code" => 200,
                "payload" => $dados
            ];
        } catch (UnauthorizedException $e) {
            return (new UnauthorizedException())->response();
        } catch (\Exception $e) {
            return (new InternalServerError())->response();
        }
    }

    /**
     * @api {get} /permissoes/cliente/{cliente_id}/{permissao_id} Busca Permissao
     * @apiName BuscaPermissao
     * @apiGroup Nivel Permissao
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function show_cliente ($cliente, $permissao) {
        try {
            $dados = $this->permissaoService->obtemDadosCliente($cliente, $permissao);

            return [
                "code" => 200,
                "payload" => $dados
            ];
        } catch (UnauthorizedException $e) {
            return (new UnauthorizedException())->response();
        } catch (\Exception $e) {
            return (new InternalServerError())->response();
        }
    }

    /**
     * @api {get} /valida-identificador/{identificador}/{id} Checa se usuario existe
     * @apiName ChecaNomePermissao
     * @apiGroup Nivel Permissao
     *
     * @apiDescription  Esta API é utilizada para verificar se o usuário já esta cadastrado no sistema.
     *                  Caso o usuário existe retornará <b>true</b>, no caso de não existir retornará <b>false</b>
     *
     * @apiParam {string} login         login do usuario
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "usuario_existe": true
     *     }
     *
     */
    public function checaIdentificador ($identificador, $id, $sistema, $cliente) {
        $dado = $this->permissaoService->checaIdentificador($identificador, $id, $sistema, $cliente);

        if (!$dado) {
            return [
                "code" => 200,
                "permissao_existe" => false
            ];
        }
        return [
            "code" => 200,
            "permissao_existe" => true
        ];
    }

    /**
     * @api {post} /permissoes Cadastra Permissao
     * @apiName CadastraPermissao
     * @apiGroup Nivel Permissao
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function store () {
        try {

            $validator = Validator::make(\request()->all(), [
                'identificador' => 'required|string'
            ]);

            if ($validator->fails()) {
                return (new PayloadRequiredException())
                    ->setExtras($validator->errors()->toArray())
                    ->response();
            }

            return $this->permissaoService->adiciona();

        }catch (\Exception $e) {
            return \response([
                'status' => 500,
                'erro' =>$e->getMessage()
            ],500 );
        }
    }

    /**
     * @api {put} /permissoes/{id} Altera Permissao
     * @apiName AlteraPermissao
     * @apiGroup Nivel Permissao
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        try {

            $validator = Validator::make(\request()->all(), [
                'identificador' => 'required|string'
            ]);

            if ($validator->fails()) {
                return (new PayloadRequiredException())
                    ->setExtras($validator->errors()->toArray())
                    ->response();
            }

            return $this->permissaoService->atualiza($id);

        } catch (UnauthorizedException $e) {
            return (new UnauthorizedException())->response();
        } catch (\Exception $e) {
            return (new InternalServerError())
                ->setError($e)
                ->response();
        }
    }

    /**
     * @api {get} /permissoes/{id} Busca Permissao
     * @apiName BuscaPermissao
     * @apiGroup Nivel Permissao
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @param int $id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function show ($id) {
        try {

            $setor = $this->permissaoService->obtemDados($id);

            if (!$setor) {
                return (new NotFoundException())->response();
            }

            return array(
                "code" => 200,
                "payload" => $setor
            );
        } catch (UnauthorizedException $e) {
            return (new UnauthorizedException())->response();
        } catch (\Exception $e) {
            return (new InternalServerError())
                ->setError($e)
                ->response();
        }
    }

    /**
     * @api {get} /permissoes-por-sistema/{id} Busca Permissao
     * @apiName BuscaPermissao
     * @apiGroup Nivel Permissao
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @param int $id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function listaPorSistema ($id) {
        try {

            $setor = $this->permissaoService->obtemDadosPorSistema($id);

            if (!$setor) {
                return (new NotFoundException())->response();
            }

            return array(
                "code" => 200,
                "payload" => $setor
            );
        } catch (UnauthorizedException $e) {
            return (new UnauthorizedException())->response();
        } catch (\Exception $e) {
            return (new InternalServerError())
                ->setError($e)
                ->response();
        }
    }

    /**
     * @api {delete} /permissoes/{id} Remove Permissao
     * @apiName RemoverPermissao
     * @apiGroup Nivel Permissao
     *
     * @apiUse UnauthorizedError
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function destroy ($id) {
        try {
            return $this->permissaoService->remove($id);

        } catch (UnauthorizedException $e) {
            return (new UnauthorizedException())->response();
        } catch (\Exception $e) {
            return (new InternalServerError())
                ->setError($e)
                ->response();
        }
    }

    protected function chaveCriptografada () {
        try {
            $key = "program@doresMtwTudoSinistros3000";
            $key = Crypt::encrypt($key);
            return [
                "code" => 200,
                "key" => $key,
            ];
        }catch (\Exception $e) {
            return \response([
                'status' => 500,
                'erro' =>$e->getMessage()
            ],500 );
        }
    }
}
