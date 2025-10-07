<?php

namespace App\Services;

use CodeIgniter\HTTP\CURLRequest;

class RagService
{
    protected $pythonServiceUrl;
    protected $client;

    public function __construct()
    {
        $this->pythonServiceUrl = 'http://localhost:5000';
        $this->client = \Config\Services::curlrequest();
    }

    public function processPdf($pdfPath, $text)
    {
        // Validasi input
        if (empty($text) || strlen($text) < 50) {
            log_message('warning', 'Text terlalu pendek untuk diproses: ' . $pdfPath);
            return false;
        }

        try {
            $response = $this->client->post($this->pythonServiceUrl . '/process_pdf', [
                'json' => [
                    'pdf_path' => $pdfPath,
                    'text' => $text
                ],
                'timeout' => 30  // Timeout 30 detik
            ]);

            $result = json_decode($response->getBody(), true);

            if (!isset($result['status']) || $result['status'] !== 'success') {
                throw new \Exception('Invalid response from RAG service');
            }

            return true;
        } catch (\Exception $e) {
            log_message('error', 'RAG Service Error: ' . $e->getMessage());
            return false;
        }
    }

    public function queryPdf($question, $pdfPath)
    {
        try {
            $docId = basename($pdfPath);
            $response = $this->client->post($this->pythonServiceUrl . '/query', [
                'json' => [
                    'question' => $question,
                    'doc_id' => $docId
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            log_message('error', 'Failed to query PDF: ' . $e->getMessage());
            return [
                'answer' => 'Sorry, the AI service is currently unavailable.',
                'sources' => []
            ];
        }
    }
}
