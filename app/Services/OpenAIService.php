<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('app.openai_api_key');
    }

    /**
     * Generate meta information from OpenAI API.
     *
     * @param string $title
     * @param string $description
     * @return array
     */
    public function generateMetaInfo(string $title, string $description): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post('https://api.openai.com/v1/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Please generate the following meta information for the article: \n\nTitle: \"$title\"\nDescription: \"$description\"\n\nMeta Title: (short, under 50 characters)\nMeta Description: (under 150 characters)\nKeywords: (comma-separated list of relevant keywords)"
                    ]
                ],
                'max_tokens' => 60,
            ]);;

            // Check if the response is successful
            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? '';
                return $this->parseMetaContent($content, $title);
            } else {
                Log::error('OpenAI API response error', [
                    'response_body' => $response->body(),
                    'status_code' => $response->status(),
                    'url' => $response->effectiveUri()
                ]);
            }
        } catch (Exception $e) {
            // do nothing handle return default meta
            Log::error('OpenAI API request failed', ['error' => $e->getMessage()]);
        }

        return [
            'meta_title' => $title,
            'meta_description' => $title,
            'keywords' => $title,
        ];
    }

    /**
     * Parse the OpenAI API response content to extract meta information.
     *
     * @param string $content
     * @return array
     */
    private function parseMetaContent(string $content, $title): array
    {
        preg_match('/Meta Title: (.*)/', $content, $metaTitle);
        preg_match('/Meta Description: (.*)/', $content, $metaDescription);
        preg_match('/Keywords: (.*)/', $content, $keywords);

        return [
            'meta_title' => $metaTitle[1] ?? $title,
            'meta_description' => $metaDescription[1] ?? $title,
            'keywords' => $keywords[1] ?? $title,
        ];
    }
}