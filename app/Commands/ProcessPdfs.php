<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\RagService;

class ProcessPdfs extends BaseCommand
{
    protected $group = 'AI';
    protected $name = 'ai:process-pdfs';
    protected $description = 'Process all PDFs for RAG system';

    public function run(array $params)
    {
        $ragService = new RagService();
        $pengetahuanModel = new \App\Models\PengetahuanModel();

        $allPdfs = $pengetahuanModel->findAll();

        foreach ($allPdfs as $pdf) {
            $pdfPath = WRITEPATH . '../public/assets/uploads/pengetahuan/' . $pdf['file_pdf_pengetahuan'];

            if (file_exists($pdfPath)) {
                CLI::write("Processing: " . $pdf['judul']);
                $ragService->processPdf($pdfPath);
                CLI::write("Done!");
            } else {
                CLI::error("File not found: " . $pdfPath);
            }
        }

        CLI::write('All PDFs processed successfully!');
    }
}
