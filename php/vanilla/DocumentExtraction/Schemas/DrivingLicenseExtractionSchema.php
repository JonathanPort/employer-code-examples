<?php

namespace App\DocumentExtraction\Schemas;

use App\DocumentExtraction\DocumentExtractionSchema;
use GuzzleHttp\Psr7\MimeType;

final class DrivingLicenseExtractionSchema extends DocumentExtractionSchema
{
    public function type(): string
    {
        return 'Driving License';
    }

    public function extractionQueries(): array
    {
        return [
            'surname' => 'What is the surname on line 1.?',
            'name' => 'What is the forename on line 2.?',
            'date_of_birth' => 'What is the date on line 3.?',
            'nationality' => 'What is the nationality on line 3.?',
            'license_issue_data' => 'What is the license issue date on line 4.a.?',
            'license_expiry_data' => 'What is the license expiry date on line 4.b.?',
            'license_issue_authority' => 'Who is the authority on line 4.c.?',
            'driving_license_number' => 'What is the content on line 5.?',
            'address' => 'What is the address on line 8.?',
        ];
    }

    public function extractionSuccessCriteriaRules(): array
    {
        return [
            'name' => 'required|string',
            'dob' => 'required|string',
            'license_number' => 'required|string',
            'address' => 'required|string',
            'expiry_date' => 'required|date',
        ];
    }

    public function acceptedMimeTypes(): array
    {
        return [
            MimeType::fromExtension('jpg'),
            MimeType::fromExtension('jpeg'),
            MimeType::fromExtension('png'),
            MimeType::fromExtension('pdf'),
        ];
    }
}
