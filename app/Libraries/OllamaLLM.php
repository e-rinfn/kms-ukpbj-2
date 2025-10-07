<?php

namespace App\Libraries;

class OllamaLLM
{
    protected $apiUrl;
    protected $model;

    public function __construct()
    {
        $this->apiUrl = getenv('OLLAMA_API_URL') ?: 'http://localhost:11434';
        $this->model = getenv('OLLAMA_MODEL') ?: 'llama3:8b';
    }

    public function generate($prompt)
    {
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->post("{$this->apiUrl}/api/generate", [
                'json' => [
                    'model' => $this->model,
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.7,
                        'max_tokens' => 1000
                    ]
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['response'] ?? 'Maaf, saya tidak bisa menjawab pertanyaan itu saat ini.';
        } catch (\Exception $e) {
            log_message('error', 'Ollama generate error: ' . $e->getMessage());
            return 'Terjadi kesalahan saat memproses permintaan Anda.';
        }
    }
}
