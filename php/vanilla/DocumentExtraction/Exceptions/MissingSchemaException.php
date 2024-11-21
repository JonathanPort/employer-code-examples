<?php

namespace App\DocumentExtraction\Exceptions;

use Exception;

class MissingSchemaException extends Exception
{
    public function __construct(string $type)
    {
        parent::__construct('Schema not found for type: '.$type);
    }
}
