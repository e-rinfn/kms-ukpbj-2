<?php

namespace App\Controllers;

use App\Libraries\PdfProcessor;

class PdfChat extends BaseController
{
    protected $pdfProcessor;

    public function __construct()
    {
        $this->pdfProcessor = new PdfProcessor();
    }

    public function chat($pengetahuanId)
    {
        // Ambil data pengetahuan dari database
        $pengetahuanModel = model('App\Models\PengetahuanModel');
        $pengetahuan = $pengetahuanModel->find($pengetahuanId);

        if (!$pengetahuan) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Dokumen tidak ditemukan']);
        }

        $pdfPath = WRITEPATH . '../public/assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan'];

        if (!file_exists($pdfPath)) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'File PDF tidak ditemukan']);
        }

        // Proses pertanyaan dari input POST
        $question = $this->request->getPost('question');
        if (empty($question)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Pertanyaan tidak boleh kosong']);
        }

        // Ekstrak teks dari PDF
        try {
            $pdfContent = $this->pdfProcessor->processPdf($pdfPath);

            if (!$pdfContent['success']) {
                throw new \Exception($pdfContent['error'] ?? 'Gagal memproses PDF');
            }

            // Dapatkan jawaban dari model LLaMA
            $answer = $this->pdfProcessor->generateAnswer($question, $pdfContent['text']);

            return $this->response->setJSON([
                'success' => true,
                'answer' => $answer,
                'context' => substr($pdfContent['text'], 0, 500) . '...' // Potongan teks sebagai konteks
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }
}
