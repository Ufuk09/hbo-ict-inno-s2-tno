<?php

namespace TNO\EssifLab\Utilities\Exceptions;

use Throwable;
use Exception;

class NotExistingRelation extends Exception {
    public function __construct($fromType, $toType, $code = 0, Throwable $previous = null) {
        $message = "Not existing relation: this $fromType is not linked to this $toType.";
        parent::__construct($message, $code, $previous);
    }
}