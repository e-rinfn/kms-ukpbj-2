<?php

namespace App\Libraries;

use CodeIgniter\HTTP\ResponseInterface;

class PdfChatService
{
    protected $pythonPath = 'python3'; // atau path lengkap ke python executable
    protected $scriptPath = APPPATH . 'ThirdParty/python/rag_pdf';

    public function processPdfAndQuery($pdfPath, $question)
    {
        // Pastikan file PDF ada
        if (!file_exists($pdfPath)) {
            throw new \RuntimeException("File PDF tidak ditemukan: {$pdfPath}");
        }

        $command = escapeshellcmd("{$this->pythonPath} {$this->scriptPath}/query_pdf.py") .
            ' ' . escapeshellarg($pdfPath) .
            ' ' . escapeshellarg($question);

        $output = shell_exec($command . ' 2>&1');

        if (strpos($output, 'ERROR:') !== false) {
            throw new \RuntimeException("Error processing PDF: " . $output);
        }

        return json_decode($output, true);
    }
}
