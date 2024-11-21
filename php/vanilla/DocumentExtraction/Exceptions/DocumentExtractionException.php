<?php

namespace App\DocumentExtraction\Exceptions;

use Exception;

class DocumentExtractionException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
