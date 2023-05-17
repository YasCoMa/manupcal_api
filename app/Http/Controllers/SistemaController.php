<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\Forbidden;
use App\Exceptions\InternalServerError;
use App\Exceptions\NotFoundException;
use App\Exceptions\PayloadRequiredException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Services\SistemaService;

use Log;
use App;
use PDF;

class SistemaController extends Controller
{
    use UtilsController;
    private $service;

    function __construct(SistemaService $service)
    {
        $this->instanciaService = $service;
        $this->middleware('jwt.auth')
        ->except([ 'show']);
    }

    /**
     * @api {get} /sistemas Lista Permissoes
     * @apiName ListaSistema
     * @apiGroup Sistema
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function index () {
        try {
            $dados = $this->instanciaService->obtemTodos();

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
     * @api {get} /sistemas/cliente/{cliente} Lista sistemas por cliente
     * @apiName ListaSistema
     * @apiGroup Sistema
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function index_cliente ($cliente) {
        try {
            $dados = $this->instanciaService->obtemTodosPorCliente($cliente);
            
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
     * @api {get} /valida-nome-sistema/{identificador}/{id} Checa se sistema existe
     * @apiName ChecaNomeSistema
     * @apiGroup Sistema
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
    public function checaIdentificador ($identificador, $id) {
        $dado = $this->instanciaService->checaIdentificador($identificador, $id);

        if (!$dado) {
            return [
                "code" => 200,
                "sistema_existe" => false
            ];
        }
        return [
            "code" => 200,
            "sistema_existe" => true
        ];
    }

    /**
     * @api {post} /sistemas Cadastra Sistema
     * @apiName CadastraSistema
     * @apiGroup Sistema
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function store () {
        try {

            $validator = Validator::make(\request()->all(), [
                'nome' => 'required|string|unique:sistemas'
            ]);

            if ($validator->fails()) {
                return (new PayloadRequiredException())
                    ->setExtras($validator->errors()->toArray())
                    ->response();
            }

            return $this->instanciaService->adiciona();

        }catch (\Exception $e) {
            return \response([
                'status' => 500,
                'erro' =>$e->getMessage()
            ],500 );
        }
    }

    /**
     * @api {put} /sistemas/{id} Altera Sistema
     * @apiName AlteraSistema
     * @apiGroup Sistema
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
                'nome' => [
                    'required',
                    Rule::unique('sistemas')->ignore($id),
                ],
            ]);

            if ($validator->fails()) {
                return (new PayloadRequiredException())
                    ->setExtras($validator->errors()->toArray())
                    ->response();
            }

            return $this->instanciaService->atualiza($id);

        } catch (UnauthorizedException $e) {
            return (new UnauthorizedException())->response();
        } catch (\Exception $e) {
            return (new InternalServerError())
                ->setError($e)
                ->response();
        }
    }

    /**
     * @api {get} /sistemas/{id} Busca Sistema
     * @apiName BuscaSistema
     * @apiGroup Sistema
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @param int $id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function show ($id) {
        try {

            $setor = $this->instanciaService->obtemDados($id);

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
     * @api {delete} /sistemas/{id} Remove Sistema
     * @apiName RemoverSistema
     * @apiGroup Sistema
     *
     * @apiUse UnauthorizedError
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function destroy ($id) {
        try {
            return $this->instanciaService->remove($id);

        } catch (UnauthorizedException $e) {
            return (new UnauthorizedException())->response();
        } catch (\Exception $e) {
            return (new InternalServerError())
                ->setError($e)
                ->response();
        }
    }

}
