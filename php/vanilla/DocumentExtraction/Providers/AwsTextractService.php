<?php

namespace App\DocumentExtraction\Providers;

use App\DocumentExtraction\DocumentExtractionService;
use App\DocumentExtraction\DocumentExtractionResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\File\File;

final class AwsTextractService extends DocumentExtractionService
{
    public function extract(string $documentBase64): DocumentExtractionResponse
    {
        $this->queryResults = [];

        $document = $this->parseBase64Document($documentBase64);
        if (! $document) {
            return $this->buildResponse(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                'Invalid document_base64.'
            );
        }

        if (! in_array($document->getMimeType(), $this->Schema->acceptedMimeTypes())) {
            return $this->buildResponse(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                'Invalid document mime type.'
            );
        }

        $this->queryResults = $this->callAmazonTextract($document);

        if (! count($this->queryResults)) {
            return $this->buildResponse(
                Response::HTTP_NO_CONTENT,
                'Document extraction has no matches.',
                $this->queryResults
            );
        }

        if (! $this->checkExtractionMeetsSuccessCriteria()) {
            return $this->buildResponse(
                Response::HTTP_NOT_ACCEPTABLE,
                'Document extraction does not meet success criteria.',
                $this->queryResults
            );
        }

        return $this->buildResponse(
            Response::HTTP_OK,
            'Document extraction successful.',
            $this->queryResults
        );
    }

    protected function buildResponse(int $status, string $message, array $data = []): DocumentExtractionResponse
    {
        return new DocumentExtractionResponse(
            $status,
            $message,
            $this->Schema->key(),
            $data,
            $this->getExtractionConfidenceScore()
        );
    }

    protected function callAmazonTextract(File $document): array
    {
        // Call Amazon Textract API
        $client = new \Aws\Textract\TextractClient([
            'region' => config('services.aws.region'),
            'version' => config('services.aws.version'),
            'credentials' => [
                'key' => config('services.aws.key'),
                'secret' => config('services.aws.secret'),
            ],
        ]);

        $response = $client->analyzeDocument([
            'Document' => [
                'Bytes' => file_get_contents($document->getPathname()),
            ],
            'QueriesConfig' => [
                'Queries' => $this->getExtractionQueries(),
            ],
            'FeatureTypes' => ['QUERIES'],
        ]);

        $queries = [];
        $queryResults = [];
        foreach ($response['Blocks'] as $block) {
            if ($block['BlockType'] == 'QUERY') {
                $queries[] = $block;
            } else if ($block['BlockType'] == 'QUERY_RESULT') {
                $queryResults[] = $block;
            }
        }

        $matches = [];
        foreach ($queries as $query) {

            if (! isset($query['Relationships'])) {
                $matches[$query['Query']['Alias']] = [
                    'key' => $query['Query']['Alias'],
                    'query' => $query['Query']['Text'],
                    'result' => null,
                    'confidence' => 0,
                ];
                continue;
            }

            $relationshipId = $query['Relationships'][0]['Ids'][0];
            foreach ($queryResults as $queryResult) {
                if ($queryResult['Id'] == $relationshipId) {
                    $matches[$query['Query']['Alias']] = [
                        'key' => $query['Query']['Alias'],
                        'query' => $query['Query']['Text'],
                        'result' => $queryResult['Text'],
                        'confidence' => $queryResult['Confidence'],
                    ];
                }
            }
        }

        return $matches;
    }

    protected function getExtractionQueries(): array
    {
        $queries = [];
        foreach ($this->Schema->extractionQueries() as $key => $query) {
            $queries[] = [
                'Text' => $query,
                'Alias' => $key,
            ];
        }
        return $queries;
    }
}
