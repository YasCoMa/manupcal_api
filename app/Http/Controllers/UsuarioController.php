<?php

namespace App\Http\Controllers;

use App\Services\UsuarioService;

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
use Illuminate\Support\Facades\DB;

use Log;

class UsuarioController extends Controller
{

    private $service;

    function __construct(UsuarioService $service)
    {
        $this->usuarioService = $service;
        $this->middleware('jwt.auth')->except(['authenticate']);
    }

    use UtilsController;

    /**
     * @api {get} /me Busca meus dados
     * @apiName Me
     * @apiGroup Usuarios
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "payload": {
     *           "nome": "Super Administrador",
     *           "email": "wsilvasjb@gmail.com",
     *           "login": "wallace",
     *           "cod_permissao": 1,
     *           "status": false,
     *           "cod_setor": 1,
     *           "uuid": "0e06c820-e4f2-11e8-99f2-1094bbc99a48",
     *           "setor": {
     *               "id": 1,
     *               "nome": "Suporte",
     *               "status": false
     *           }
     *         }
     *     }
     *
     * @apiUse UnauthorizedError
     */
    public function me() {
        $usuario = auth()->user();
        return [
            "code" => 200,
            "payload" => $usuario
        ];
    }

    /**
     * @param AuthenticateRequest $request
     * @return array|JsonResponse
     * @api {post} /public/autentica Autentica Usuário
     * @apiName Auth
     * @apiGroup Usuarios
     *
     * @apiParam {String} login         Usuário para autenticação
     * @apiParam {String} senha         Senha para autenticação
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "payload": {
     *          "nome": "Wallace Silva",
     *          "jwt": "jgseafadfgdfgsdfg.adfighghsdjkfgsdfgsd.jdhgfb7dfg6786g78ad"
     *       }
     *     }
     *
     * @apiUse UnauthorizedError
     */
    public function authenticate(AuthenticateRequest $request)
    {

        $validator = Validator::make(request()->all(), [
            'login' => 'required|string',
            'senha'=> 'required',
            'codigo'=> 'required'
        ]);

        if ($validator->fails()) {
            return array(
                "code" => 200,
                "payload" => array(
                    "error" => "Os campos login e senha são obrigatórios!"
                )
            );
        }

        $credentials = request()->only('login', 'senha', 'codigo','sistema_id');

        try {
            $data = [
                'secret' => '6Ldn5rUhAAAAAFIIK7WGRayxM1KUw7pio3hQaqJ8',
                'response' => $credentials['codigo']
            ];

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

            $response = curl_exec($curl);
            $response = json_decode($response, true);

            if ($response['success'] === false and $credentials['codigo']!="none" ) {
                return array(
                    "code" => 200,
                    'resp' => $response,
                    "payload" => array(
                        "error" => "Verificação de captcha inválida"
                    )
                );

            }
            else {
                $usuario = $this->usuarioService->autenticaUsuario($credentials['login'], $credentials['senha'], $credentials['sistema_id']);

                if($usuario->status){
                    //if (! $token = JWTAuth::fromUser($usuario, ["exp" => time() + (4 * 60 * 1)])) {
                    if (! $token = JWTAuth::fromUser($usuario) ) {
                            /*return (new UnauthorizedException())
                            ->setExtra("erro", "criação do jwt")
                            ->response();*/

                        return    array(
                                "code" => 200,
                                "payload" => array(
                                    "error" => "Login não pôde ser feito, verifique as informações de login e senha."
                                )
                        );
                    }

                    return array(
                        "code" => 200,
                        "payload" => array(
                            "error" => "",
                            "jwt" => $token,
                            "nome" => $usuario->nome,
                            "usuario" => $usuario
                        )
                    );
                }
                else{
                    return array(
                                "code" => 200,
                                "payload" => array(
                                    "error" => "Usuário está desativado."
                                )
                        );
                }
            }

        } catch (UnauthorizedException $e) {
            return    array(
                "code" => 200,
                "payload" => array(
                    "error" => "Login não pôde ser feito, verifique as informações de login e senha."
                )
            );
            //return $e->response();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(array("error" => $e->getMessage()), 500);
        }
    }

    /**
     * @api {get} /usuarios Lista usuarios
     * @apiName ListaUsuarios
     * @apiGroup Usuarios
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "payload": [
     *          {
     *              "uuid": '634adfdb-0513-4c33-bc41-03eb8c0c0ad1',
     *              "nome": "Usuário - Suporte",
     *              "cod_setor": 1,
     *              "setor": {
     *                  "id": 1,
     *                  "nome": "Suporte",
     *                  "status": false
     *              }
     *              "email": "suporte@mtw.com.br",
     *              "login": "superuser",
     *              "cod_permissao": 1,
     *              "status": true
     *          },
     *          {
     *              "uuid": '634adfdb-0513-4c33-bc41-03eb8c0c0ad1',
     *              "nome": "Usuário2 - Suporte",
     *              "cod_setor": 1,
     *              "setor": {
     *                  "id": 1,
     *                  "nome": "Suporte",
     *                  "status": false
     *              }
     *              "email": "suporte@mtw.com.br",
     *              "login": "superuser2",
     *              "cod_permissao": 2,
     *              "status": true
     *          }
     *      ]
     *     }
     *
     * @apiUse UnauthorizedError
     */
    public function index () {
        $usuarios = $this->usuarioService->obtemTodos();
        return [
            "code" => 200,
            "payload" => $usuarios
        ];
    }

    /**
     * @api {get} /usuarios/sistema/{sistema_id} Lista usuarios
     * @apiName ListaUsuarios
     * @apiGroup Usuarios
     *
     * @apiUse UnauthorizedError
     */
    public function index_sistema ($sistema_id) {
        $usuarios = $this->usuarioService->obtemTodosSistema($sistema_id);
        return [
            "code" => 200,
            "payload" => $usuarios
        ];
    }

    /**
     * @api {get} /usuarios/{uuid} Busca 1 usuario
     * @apiName BuscaUsuario
     * @apiGroup Usuarios
     *
     * @apiParam {Int} uuid         UUID do usuario
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "payload": {
     *           "nome": "Super Administrador",
     *           "email": "wsilvasjb@gmail.com",
     *           "login": "wallace",
     *           "cod_permissao": 1,
     *           "status": false,
     *           "cod_setor": 1,
     *           "uuid": "0e06c820-e4f2-11e8-99f2-1094bbc99a48",
     *           "setor": {
     *               "id": 1,
     *               "nome": "Suporte",
     *               "status": false
     *           }
     *         }
     *     }
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     */
    public function show ($id) {
        $usuario = $this->usuarioService->obtemDadosUsuario($id);
        if (!$usuario) {
            return (new NotFoundException())->response();
        }

        if(isset($usuario["code"]) == 422){
            return \response([
                'code' => 422,
                "erro" => "Erro",
                "validacao" => "Permissão Negada!",
            ],422 );
        }
        return [
            "code" => 200,
            "payload" => $usuario
        ];
    }

    /**
     * @api {get} /usuarios/sistema/{sistema_id}/{uuid} Busca 1 usuario
     * @apiName BuscaUsuario
     * @apiGroup Usuarios
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     */
    public function show_sistema ($sistema_id, $id) {
        $usuario = $this->usuarioService->obtemDadosUsuarioSistema($sistema_id, $id);
        if (!$usuario) {
            return (new NotFoundException())->response();
        }

        if(isset($usuario["code"]) == 422){
            return \response([
                'code' => 422,
                "erro" => "Erro",
                "validacao" => "Permissão Negada!",
            ],422 );
        }
        return [
            "code" => 200,
            "payload" => $usuario
        ];
    }

    /**
     * @api {get} /usuarios-por-cliente/{id} Busca usuario
     * @apiName BuscaUsuario
     * @apiGroup Usuario
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     * @param int $id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function listaPorCliente ($id) {
        try {

            $setor = $this->usuarioService->obtemDadosPorCliente($id);

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
     * @api {get} /valida-login/{login}/{id} Checa se login existe
     * @apiName ChecaUsuario
     * @apiGroup Usuarios
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
    public function checaLogin ($login, $id) {
        $usuario = $this->usuarioService->checaExistenciaLogin($login, $id);

        if (!$usuario) {
            return [
                "code" => 200,
                "usuario_existe" => false
            ];
        }
        return [
            "code" => 200,
            "usuario_existe" => true
        ];
    }

    /**
     * @api {get} /valida-email/{email}/{id} Checa se email existe
     * @apiName ChecaEmail
     * @apiGroup Usuarios
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
    public function checaEmail ($email, $id) {
        $usuario = $this->usuarioService->checaExistenciaEmail($email, $id);

        if (!$usuario) {
            return [
                "code" => 200,
                "usuario_existe" => false
            ];
        }
        return [
            "code" => 200,
            "usuario_existe" => true
        ];
    }

    /**
     * @api {delete} /usuarios/{uuid} Remove usuario
     * @apiName Remove Usuario
     * @apiGroup Usuarios
     *
     * @apiParam {String} uuid         UUID do usuario
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "uuid": "634adfdb-0513-4c33-bc41-03eb8c0c0ad3"
     *     }
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     */
    public function destroy ($id) {
        try {
            return $this->usuarioService->remove($id);

            /*$modulo="Usuário";
            $idLogged = Auth::id();
            AcoesUsuario::create([
                'id_usuario' => $idLogged,
                'tipo_operacao' => 'Exclusão',
                'tabela_afetada' => $modulo,
                'data_hora' => \Carbon\Carbon::now(),
                'descricao' => 'Nome: '.($titulo).' <br />IP de Acesso: '.($this->get_client_ip())
            ]);*/

        } catch (\Exception $e) {
            return (new InternalServerError())->setError($e)->response();
        }
    }

    /**
     * @api {put} /usuarios/{uuid} Altera usuarios
     * @apiName AlteraUsuario
     * @apiGroup Usuarios
     *
     * @apiParam {String} uuid                       Codigo do usuario a ser alterado
     * @apiParam {String} nome                      Nome do Usuario
     * @apiParam {Integer} cod_permissao            Codigo da permissao
     * @apiParam {String} email                     Email do Usuario
     * @apiParam {Integer} cod_setor                Codigo do Setor
     * @apiParam {Boolean} status                   Status do usuario
     * @apiParam {String} login                     Login do Usuario
     * @apiParam {String} senha                     Senha para ser alterada
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "uuid": '634adfdb-0513-4c33-bc41-03eb8c0c0ad1'
     *     }
     *
     * @apiUse UnauthorizedError
     * @apiUse UserNotFoundError
     *
     * @apiErrorExample PayloadRequired
     *     HTTP/1.1 403 PayloadRequired
     *     {
     *         "code": 402,
     *         "message": "Payload Required",
     *         "extras": {
     *             "action": "senha_vazia"
     *         }
     *     }
     *
     * @apiError PayloadRequired action: <b>senha_vazia</b> => Caso o campo senha tenha sido passado em branco
     */
    public function update ($id) {
        try {
            $validator = Validator::make(request()->all(), [
                'login' => [
                    Rule::unique('usuarios')->ignore($id, "id"),
                ],
                'email' => 'string|email',
            ]);

            if ($validator->fails()) {
                return (new PayloadRequiredException())
                    ->setExtras("mensagem", "Já existe um usuário com o mesmo nome!")
                    ->response();
            }

            $request = request()->all();


            $usuario = $this->usuarioService->atualiza(request()->all(), $id);

            return $usuario;
        }catch (\Exception $e) {
            return \response([
                'status' => 500,
                'erro' =>$e->getMessage()
            ],500 );
        }
        /*} catch (\Exception $e) {
            Log::info($e->getMessage());
            return (new InternalServerError())->setError($e)->response();
        }*/
    }

    /**
     * @api {post} /usuarios Cadastra usuarios
     * @apiName CadastraUsuario
     * @apiGroup Usuarios
     *
     *
     *
     * @apiParam {String} nome                      Nome do Usuario
     * @apiParam {Integer} cod_permissao            Codigo da permissao
     * @apiParam {String} email                     Email do Usuario
     * @apiParam {Boolean} status                   Status do usuario
     * @apiParam {String} login                     Login do Usuario
     * @apiParam {String} senha                     Senha do usuario em formato texto plano
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "uuid": '634adfdb-0513-4c33-bc41-03eb8c0c0ad1'
     *     }
     *
     * @apiError PayloadRequired action: <b>usuario_cadastrado</b> => Usuarios já cadastrados
     *
     * @apiErrorExample PayloadRequired
     *     HTTP/1.1 403 PayloadRequired
     *     {
     *         "code": 402,
     *         "message": "Payload Required",
     *         "extras": {
     *             "action": "usuario_cadastrado"
     *         }
     *     }
     *
     * @apiUse UnauthorizedError
     */
    public function store () {
        try {
            $request = request()->all();

            $validator = Validator::make($request, [
                'nome' => 'required|string',
                'login' => 'required|string|unique:usuarios',
                'email' => 'required|email',
                'senha' => 'required'
            ]);

            if ($validator->fails()) {
                return (new PayloadRequiredException())
                    ->setExtras("mensagem", "Já existe um usuário com o mesmo nome!")
                    ->response();
            }

            $usuario = $this->usuarioService->adiciona($request);
            return $usuario;

        } catch (\Exception $e) {
            return (new InternalServerError())->setError($e)->response();
        }
    }

    public function enviaEmailToken(){
        try{
            $token=uniqid();
            $token=substr($token, 0, 6);
            $idLogged = Auth::id();
            $usuario = $this->usuarioService->obtemDadosUsuario($idLogged);

            $mensagem= "Olá, <br />
                <p>Você está recebendo este e-mail para continuar sua autenticação no sistema do Almoxarifado.</p>
                <p>Este é o seu <b>token</b>: $token</p>
                <p>Se não requisitou o login, ignore este e-mail</p>
            ";
            $code=200;

            //$resp = $this->envia_email_service($usuario->email, "Continuação de autenticação - Portalgov Sistema", $mensagem, $usuario->nome);
            //$code=$resp->code;
            $details = [
                'assunto' => 'Continuação de autenticação - Almoxarifado Sistema',
                'mensagem' => $mensagem
            ];

            \Mail::to($usuario->email)->send(new \App\Mail\SenderMail($details));

            if($code==200){
                return array(
                    "code" => 200,
                    "payload" => array(
                        "token" => $token
                    )
                );

            }
            else{
                return array(
                    "code" => $resp->code,
                    "payload" => array(
                        "token" => null
                    )
                );
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(array("error" => $e->getMessage()), 500);
        }
    }

}
