<?php

namespace App\Libraries;

class OllamaEmbeddings
{
    protected $apiUrl;
    protected $model;

    public function __construct()
    {
        $this->apiUrl = getenv('OLLAMA_API_URL') ?: 'http://localhost:11434';
        $this->model = getenv('OLLAMA_EMBEDDING_MODEL') ?: 'nomic-embed-text';
    }

    public function embed($text)
    {
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->post("{$this->apiUrl}/api/embeddings", [
                'json' => [
                    'model' => $this->model,
                    'prompt' => $text
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['embedding'] ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Ollama embedding error: ' . $e->getMessage());
            return [];
        }
    }
}
