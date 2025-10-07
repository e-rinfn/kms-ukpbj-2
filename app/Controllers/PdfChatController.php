<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;
use App\Models\PengetahuanModel;
use Smalot\PdfParser\Parser;

class PdfChatController extends Controller
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new PengetahuanModel();
        helper(['filesystem', 'text']);
    }

    /**
     * Proses upload dan parsing PDF
     */
    public function uploadPdf()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'judul' => 'required|max_length[255]',
            'pdf_file' => 'uploaded[pdf_file]|ext_in[pdf_file,pdf]|max_size[pdf_file,5120]',
            'caption' => 'permit_empty|max_length[500]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $file = $this->request->getFile('pdf_file');
        $judul = $this->request->getPost('judul');
        $caption = $this->request->getPost('caption');

        // Simpan file PDF
        $newName = $file->getRandomName();
        $file->move(WRITEPATH . '../public/assets/uploads/pengetahuan/', $newName);

        // Parse PDF dan simpan teks untuk embedding
        $pdfText = $this->parsePdf(WRITEPATH . '../public/assets/uploads/pengetahuan/' . $newName);

        // Simpan ke database
        $data = [
            'judul' => $judul,
            'file_pdf_pengetahuan' => $newName,
            'caption_pengetahuan' => $caption,
            'user_id' => session()->get('id')
        ];

        if ($this->model->save($data)) {
            // Proses embedding (akan diimplementasikan nanti)
            $this->processEmbedding($this->model->getInsertID(), $pdfText);

            return redirect()->to('/pengetahuan')->with('message', 'PDF berhasil diupload dan diproses');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data');
    }

    /**
     * Parse PDF menjadi teks
     */
    protected function parsePdf($path)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        return $pdf->getText();
    }

    /**
     * Proses embedding teks ke vector database
     */
    protected function processEmbedding($pengetahuanId, $text)
    {
        // Implementasi embedding menggunakan Ollama atau layanan lain
        // Ini adalah placeholder - implementasi aktual tergantung pada library yang digunakan

        // Pertama, split teks menjadi chunks
        $chunks = $this->splitText($text);

        // Kemudian buat embedding untuk setiap chunk
        $embeddings = [];
        foreach ($chunks as $chunk) {
            $embeddings[] = $this->generateEmbedding($chunk);
        }

        // Simpan embeddings ke database atau vector store
        $this->saveEmbeddings($pengetahuanId, $chunks, $embeddings);
    }

    /**
     * Split teks menjadi chunks
     */
    protected function splitText($text, $chunkSize = 1000, $overlap = 200)
    {
        $chunks = [];
        $length = mb_strlen($text);
        $start = 0;

        while ($start < $length) {
            $end = min($start + $chunkSize, $length);
            $chunk = mb_substr($text, $start, $end - $start);
            $chunks[] = $chunk;
            $start = $end - $overlap;
        }

        return $chunks;
    }

    /**
     * Generate embedding untuk teks
     */
    protected function generateEmbedding($text)
    {
        // Implementasi aktual tergantung pada library embedding yang digunakan
        // Contoh menggunakan OllamaEmbeddings
        try {
            $embeddingModel = new \App\Libraries\OllamaEmbeddings();
            return $embeddingModel->embed($text);
        } catch (\Exception $e) {
            log_message('error', 'Embedding error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Simpan embeddings ke database
     */
    protected function saveEmbeddings($pengetahuanId, $chunks, $embeddings)
    {
        // Simpan ke database atau vector store seperti Chroma
        $vectorStore = new \App\Libraries\VectorStore();
        $vectorStore->saveDocumentChunks($pengetahuanId, $chunks, $embeddings);
    }

    /**
     * API untuk chat dengan PDF
     */
    public function chatWithPdf()
    {
        $pengetahuanId = $this->request->getPost('pengetahuan_id');
        $question = $this->request->getPost('question');

        // Validasi input
        if (empty($pengetahuanId) || empty($question)) {
            return $this->failValidationError('ID pengetahuan dan pertanyaan harus diisi');
        }

        // Proses pertanyaan dengan RAG
        $response = $this->ragQuery($pengetahuanId, $question);

        return $this->respond([
            'status' => 'success',
            'answer' => $response['answer'],
            'sources' => $response['sources']
        ]);
    }

    /**
     * Proses query RAG
     */
    protected function ragQuery($pengetahuanId, $question)
    {
        // 1. Retrieve relevant chunks
        $relevantChunks = $this->retrieveRelevantChunks($pengetahuanId, $question);

        // 2. Format context
        $context = implode("\n\n", array_column($relevantChunks, 'text'));

        // 3. Generate answer
        $answer = $this->generateAnswer($question, $context);

        return [
            'answer' => $answer,
            'sources' => array_slice($relevantChunks, 0, 3) // Ambil 3 sumber teratas
        ];
    }

    /**
     * Retrieve relevant chunks dari vector store
     */
    protected function retrieveRelevantChunks($pengetahuanId, $question)
    {
        $vectorStore = new \App\Libraries\VectorStore();

        // Dapatkan embedding untuk pertanyaan
        $questionEmbedding = $this->generateEmbedding($question);

        // Query vector store
        return $vectorStore->query($pengetahuanId, $questionEmbedding, 5); // Ambil 5 chunk teratas
    }

    /**
     * Generate jawaban menggunakan LLM
     */
    protected function generateAnswer($question, $context)
    {
        $llm = new \App\Libraries\OllamaLLM();

        $prompt = "Anda adalah asisten yang membantu menjawab pertanyaan berdasarkan konteks yang diberikan. 
        Jawablah pertanyaan berikut dalam bahasa Indonesia dengan jelas dan ringkas berdasarkan konteks di bawah ini.
        
        Konteks:
        {$context}
        
        Pertanyaan: {$question}
        
        Jawaban:";

        return $llm->generate($prompt);
    }
}
