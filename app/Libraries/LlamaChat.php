<?php

namespace App\Libraries;

use Exception;

class LlamaChat
{
    protected $pythonPath = 'python';
    protected $scriptsPath = APPPATH . 'python_scripts';

    public function __construct()
    {
        // Konfigurasi path Python jika diperlukan
        // $this->pythonPath = 'C:/path/to/python.exe';
    }

    /**
     * Mendapatkan jawaban dari model LLaMA
     */
    public function generateAnswer($question, $context)
    {
        $script = $this->scriptsPath . '/llama_inference.py';
        $contextJson = escapeshellarg(json_encode($context));
        $question = escapeshellarg($question);

        $command = escapeshellcmd("{$this->pythonPath} {$script} {$question} {$contextJson}");

        $output = shell_exec($command);

        if (empty($output)) {
            throw new Exception('Gagal mendapatkan jawaban dari model');
        }

        return trim($output);
    }

    /**
     * Membuat embedding dari teks (untuk similarity search)
     */
    public function generateEmbeddings($text)
    {
        $script = $this->scriptsPath . '/generate_embeddings.py';
        $text = escapeshellarg($text);

        $command = escapeshellcmd("{$this->pythonPath} {$script} {$text}");

        $output = shell_exec($command);
        $result = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Gagal memproses hasil embedding: ' . json_last_error_msg());
        }

        return $result;
    }
}
