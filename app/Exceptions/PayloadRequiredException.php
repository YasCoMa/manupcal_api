<?php

namespace App\Exceptions;


class PayloadRequiredException extends ExceptionBase
{

// Redefine a exceção de forma que a mensagem não seja opcional
    public function __construct($message = "Payload Required", $code = 402, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    // personaliza a apresentação do objeto como string
    public function __toString() {
        return "Internal Server Error";
    }
}