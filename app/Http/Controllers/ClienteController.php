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

use App\Services\ClienteService;

use Log;
use App;
use PDF;

class ClienteController extends Controller
{
    use UtilsController;
    private $service;

    function __construct(ClienteService $service)
    {
        $this->instanciaService = $service;
        $this->middleware('jwt.auth')
        ->except([ 'show']);
    }

    /**
     * @api {get} /clientes Lista Permissoes
     * @apiName ListaCliente
     * @apiGroup Cliente
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
     * @api {get} /valida-nome-cliente/{identificador}/{id} Checa se cliente existe
     * @apiName ChecaNomeCliente
     * @apiGroup Cliente
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
                "cliente_existe" => false
            ];
        }
        return [
            "code" => 200,
            "cliente_existe" => true
        ];
    }

    /**
     * @api {post} /clientes Cadastra Cliente
     * @apiName CadastraCliente
     * @apiGroup Cliente
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function store (Request $request) {
        try {

            $validator = Validator::make(\request()->all(), [
                'nome' => 'required|string|unique:clientes'
            ]);

            if ($validator->fails()) {
                return (new PayloadRequiredException())
                    ->setExtras($validator->errors()->toArray())
                    ->response();
            }

            return $this->instanciaService->adiciona($request);

        }catch (\Exception $e) {
            return \response([
                'status' => 500,
                'erro' =>$e->getMessage()
            ],500 );
        }
    }

    /**
     * @api {put} /clientes/{id} Altera Cliente
     * @apiName AlteraCliente
     * @apiGroup Cliente
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {

            $validator = Validator::make(\request()->all(), [
                'nome' => [
                    'required',
                    Rule::unique('clientes')->ignore($id),
                ],
            ]);

            if ($validator->fails()) {
                return (new PayloadRequiredException())
                    ->setExtras($validator->errors()->toArray())
                    ->response();
            }

            return $this->instanciaService->atualiza($request, $id);

        } catch (UnauthorizedException $e) {
            return (new UnauthorizedException())->response();
        } catch (\Exception $e) {
            return (new InternalServerError())
                ->setError($e)
                ->response();
        }
    }

    /**
     * @api {post} /clientes/imagem/{id} Altera Cliente img
     * @apiName AlteraCliente
     * @apiGroup Cliente
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function update_brazao(Request $request, $id)
    {
        try {

            return $this->instanciaService->troca_brazao($request, $id);

        } catch (UnauthorizedException $e) {
            return (new UnauthorizedException())->response();
        } catch (\Exception $e) {
            return (new InternalServerError())
                ->setError($e)
                ->response();
        }
    }

    /**
     * @api {get} /clientes/{id} Busca Cliente
     * @apiName BuscaCliente
     * @apiGroup Cliente
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
     * @api {delete} /clientes/{id} Remove Cliente
     * @apiName RemoverCliente
     * @apiGroup Cliente
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
