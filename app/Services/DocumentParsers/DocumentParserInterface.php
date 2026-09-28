<?php

namespace App\Services\DocumentParsers;

interface DocumentParserInterface
{
    /**
     * Parse Google Document AI response and extract structured data.
     *
     * @param  array  $googleAIResponse  Response from Google Document AI API
     * @return array Structured data with items, confidence, warnings, and errors
     */
    public function parse(array $googleAIResponse): array;
}
