<?php

namespace TNO\EssifLab\Utilities\Exceptions;

use Throwable;
use Exception;

class ExistingRelation extends Exception {
    public function __construct($postType = "", $code = 0, Throwable $previous = null) {
        $message = "Existing relation: this '$postType' is already linked to this.";
        parent::__construct($message, $code, $previous);
    }
}