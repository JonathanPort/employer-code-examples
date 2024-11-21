<?php

namespace App\DocumentExtraction;

use App\DocumentExtraction\DocumentExtractionSchema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

abstract class DocumentExtractionService
{

    protected DocumentExtractionSchema $Schema;
    protected array $queryResults = [];

    abstract public function extract(string $documentBase64): DocumentExtractionResponse;

    public function setSchema(DocumentExtractionSchema $Schema): static
    {
        $this->Schema = $Schema;
        return $this;
    }

    protected function checkExtractionMeetsSuccessCriteria(): bool
    {
        return true;
    }

    protected function getExtractionConfidenceScore(): int|false
    {
        $totalScore = 0;
        $totalQueries = count($this->Schema->extractionQueries());

        foreach ($this->queryResults as $queryResult) {
            $totalScore += $queryResult['confidence'];
        }
        return $totalScore / $totalQueries;
    }

    protected function parseBase64Document(string $documentBase64): File|false
    {
        $decoded = base64_decode($documentBase64, true);
        if (! $decoded) return false;

        $tmpFilePath = sys_get_temp_dir() . '/' . Str::random();
        file_put_contents($tmpFilePath, $decoded);

        return new File($tmpFilePath);
    }
}
