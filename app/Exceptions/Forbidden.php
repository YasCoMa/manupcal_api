<?php
namespace App\Exceptions;


use Exception;

class Forbidden extends ExceptionBase
{
    public function __construct($message = "Forbidden", $code = 403, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    // personaliza a apresentação do objeto como string
    public function __toString() {
        return "Forbidden";
    }
}