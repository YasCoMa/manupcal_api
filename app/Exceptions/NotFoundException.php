<?php

namespace App\Exceptions;

/**
 * @apiDefine UserNotFoundError
 *
 * @apiError UserNotFound Nao encontrado
 *
 * @apiErrorExample Not-Found
 *     HTTP/1.1 404 Not Found
 *     {
 *       "code": 404
 *     }
 */
class NotFoundException extends ExceptionBase
{
    // Redefine a exceção de forma que a mensagem não seja opcional
    public function __construct($message =  "Not Found", $code = 404, \Exception $previous = null) {
        // código
        // garante que tudo está corretamente inicializado
        parent::__construct($message, $code, $previous);
    }

    // personaliza a apresentação do objeto como string
    public function __toString() {
        return "Not Found";
    }
}