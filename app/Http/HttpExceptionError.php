<?php

namespace App\Http;

use Exception;
use Throwable;
use App\Utils\View;

class HttpExceptionError extends Exception {

    // Redefine the exception so message isn't optional
    public function __construct($code = 0, Throwable $previous = null) {
        $this->message = View::render("pages/default_error/{$code}");

        // make sure everything is assigned properly
        parent::__construct($this->message, $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}