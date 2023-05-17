<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\Forbidden;
use App\Exceptions\InternalServerError;
use App\Exceptions\NotFoundException;
use App\Exceptions\PayloadRequiredException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Services\HabilitacaoService;

use Log;
use App;
use PDF;

class HabilitacaoController extends Controller
{
    use UtilsController;
    private $service;

    function __construct(HabilitacaoService $service)
    {
        $this->instanciaService = $service;
        $this->middleware('jwt.auth');
    }

    /**
     * @api {get} /habilitacoes Lista Habilitacoes
     * @apiName ListaHabilitacao
     * @apiGroup Habilitacao
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
     * @api {get} /habilitacoes/usuario/{usuario} Lista Habilitacoes por usuario
     * @apiName ListaHabilitacao
     * @apiGroup Habilitacao
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function index_usuario ($usuario) {
        try {
            $dados = $this->instanciaService->obtemTodosPorUsuario($usuario);

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
     * @api {post} /habilitacoes Cadastra Habilitacao
     * @apiName CadastraHabilitacao
     * @apiGroup Habilitacao
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function store () {
        try {

            $validator = Validator::make(\request()->all(), [
                'sistema_id' => 'required|integer',
                'permissao_id' => 'required|integer',
                'usuario_id' => 'required|integer'
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
     * @api {put} /habilitacoes/{id} Altera Habilitacao
     * @apiName AlteraHabilitacao
     * @apiGroup Habilitacao
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
                'permissao_id' => 'required|integer',
                'usuario_id' => 'required|integer'
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
     * @api {get} /habilitacoes/{id} Busca Habilitacao
     * @apiName BuscaHabilitacao
     * @apiGroup Habilitacao
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
     * @api {delete} /habilitacoes/{id} Remove Habilitacao
     * @apiName RemoverHabilitacao
     * @apiGroup Habilitacao
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
