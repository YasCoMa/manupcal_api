<?php
namespace App\Exceptions;

/**
 * @apiDefine UnauthorizedError
 *
 * @apiError Unauthorized Nao permitido<br>
 *              Caso retorne um <b>token_expired</b> favor solicitar um refresh do token<br>
 *              <br>action: <b>token_not_provided</b> => Não autorizado por token não informado
 *              <br>action: <b>token_expired</b> => Não autorizado por token expirado
 *              <br>action: <b>token_invalid</b> => Não autorizado por token inválido
 *
 * @apiErrorExample Unauthorized
 *     HTTP/1.1 401 Not Found
 *     {
 *       "code": 401,
 *       "message": "Unauthorized",
 *       "extras": {
 *          "action": ...
 *       }
 *     }
 */
class UnauthorizedException extends ExceptionBase
{
    public function __construct($message = "Unauthorized", $code = 401, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    // personaliza a apresentação do objeto como string
    public function __toString() {
        return "Unauthorized";
    }
}
