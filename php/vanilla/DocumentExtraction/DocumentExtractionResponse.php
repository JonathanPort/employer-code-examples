<?php

namespace App\DocumentExtraction;

final class DocumentExtractionResponse
{
    public function __construct(
        public readonly int $status,
        public readonly string $message,
        public readonly string $schemaKey,
        public readonly array $extractedData,
        public readonly float $totalConfidence
    )
    {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function __toString(): string
    {
        return $this->toJson();
    }

    public function status(): string
    {
        return $this->status;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function schemaKey(): string
    {
        return $this->schemaKey;
    }

    public function extractedData(): array
    {
        return $this->extractedData;
    }
}
