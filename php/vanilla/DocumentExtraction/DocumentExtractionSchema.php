<?php

namespace App\DocumentExtraction;

use App\DocumentExtraction\Exceptions\MissingSchemaException;
use Illuminate\Support\Str;

abstract class DocumentExtractionSchema
{

    abstract public function type(): string;

    abstract public function extractionQueries(): array;

    abstract public function extractionSuccessCriteriaRules(): array;

    abstract public function acceptedMimeTypes(): array;

    public function key(): string
    {
        return Str::snake($this->type());
    }

    public static function getSchemaFromType(string $type): DocumentExtractionSchema
    {
        $SchemaClass = 'App\\DocumentExtraction\\Schemas\\'.Str::studly($type).'ExtractionSchema';
        if (! class_exists($SchemaClass)) {
            throw new MissingSchemaException($type);
        }
        return new $SchemaClass;
    }
}
