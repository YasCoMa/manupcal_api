<?php

namespace App\Exceptions;

use Exception;

class ExceptionBase extends Exception
{

    /**
     * @var array
     */
    private $extra = [];

    /**
     * @param array $extra
     * @return $this
     */
    public function setExtras(array $extra) {
        $this->extra[] = $extra;
        return $this;
    }

    public function setExtra($key,  $value) {
        $this->extra[$key] = $value;
        return $this;
    }

    /**
     * @param Exception $e
     * @return $this
     */
    public function setError (Exception $e) {
        $this->extra["error"] = $e->getMessage();
        return $this;
    }

    public function response() {
        return response()
            ->json(array(
                "code" => $this->getCode(),
                "message" => $this->getMessage(),
                "extras" => $this->extra
            ), $this->getCode());
    }

}