<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\Forbidden;
use App\Exceptions\InternalServerError;
use App\Exceptions\NotFoundException;
use App\Exceptions\PayloadRequiredException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Services\ConcessaoService;

use Log;
use App;
use PDF;

class ConcessaoController extends Controller
{
    use UtilsController;
    private $service;

    function __construct(ConcessaoService $service)
    {
        $this->instanciaService = $service;
        $this->middleware('jwt.auth');
    }

    /**
     * @api {get} /concessoes Lista Concessoes
     * @apiName ListaConcessao
     * @apiGroup Concessao
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
     * @api {get} /concessoes/cliente/{cliente} Lista Concessoes por cliente
     * @apiName ListaConcessao
     * @apiGroup Concessao
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
     * @api {post} /concessoes Cadastra Concessao
     * @apiName CadastraConcessao
     * @apiGroup Concessao
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function store () {
        try {

            $validator = Validator::make(\request()->all(), [
                'sistema_id' => 'required|integer',
                'cliente_id' => 'required|integer'
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
     * @api {put} /concessoes/{id} Altera Concessao
     * @apiName AlteraConcessao
     * @apiGroup Concessao
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
                'sistema_id' => 'required|integer',
                'cliente_id' => 'required|integer'
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
     * @api {get} /concessoes/{id} Busca Concessao
     * @apiName BuscaConcessao
     * @apiGroup Concessao
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
     * @api {delete} /concessoes/{id} Remove Concessao
     * @apiName RemoverConcessao
     * @apiGroup Concessao
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
