<?php
namespace App\Exceptions;

use Exception;

class InternalServerError extends ExceptionBase
{
    public function __construct($message = "Internal Server Error", $code = 500, Exception $previous = null) {

        parent::__construct($message, $code, $previous);
    }

    // personaliza a apresentação do objeto como string
    public function __toString() {
        return "Internal Server Error";
    }
}