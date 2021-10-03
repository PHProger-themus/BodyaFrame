<?php

namespace system\core;

use Exception;

class SystemException extends Exception {
    
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString() {
        return System::renderBlock('Exception', [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'file' => $this->getFile(),
            'line' => $this->getLine()
        ], true);
    }
    
}
