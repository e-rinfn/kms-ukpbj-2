<?php

namespace App\Libraries;

use Exception;

class PdfProcessor
{
    protected $pythonPath = 'python';
    protected $scriptsPath = APPPATH . 'python_scripts';

    public function __construct()
    {
        // Anda bisa mengkonfigurasi path python jika diperlukan
        // $this->pythonPath = 'C:/path/to/python.exe';
    }

    public function processPdf($pdfPath)
    {
        $script = $this->scriptsPath . '/process_pdf.py';
        $command = escapeshellcmd("{$this->pythonPath} {$script} " . escapeshellarg($pdfPath));

        $output = shell_exec($command);
        $result = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to parse PDF processing result: ' . json_last_error_msg());
        }

        return $result;
    }

    public function generateAnswer($question, $context)
    {
        $script = $this->scriptsPath . '/llama_inference.py';
        $contextJson = json_encode($context);

        $command = escapeshellcmd("{$this->pythonPath} {$script} " .
            escapeshellarg($question) . " " .
            escapeshellarg($contextJson));

        $output = shell_exec($command);
        return trim($output);
    }
}
