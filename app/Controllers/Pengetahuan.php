<?php

namespace App\Controllers;

use App\Models\PengetahuanModel;
use App\Models\UserModel;

class Pengetahuan extends BaseController
{
    protected $pengetahuanModel;
    protected $userModel;

    public function __construct()
    {
        $this->pengetahuanModel = new PengetahuanModel();
        $this->userModel = new UserModel();
    }

    // public function index()
    // {
    //     $data = [
    //         'title' => 'Daftar Pengetahuan',
    //         'pengetahuan' => $this->pengetahuanModel->getPengetahuanWithUser(),
    //     ];
    //     return view('pengetahuan/index', $data);
    // }

    public function index()
    {
        $keyword = $this->request->getGet('q'); // Sesuai dengan name di form

        $data = [
            'title' => 'Daftar Pengetahuan',
            'pengetahuan' => $this->pengetahuanModel->getPengetahuanWithSearch($keyword),
            'pager' => $this->pengetahuanModel->pager,
            'keyword' => $keyword,
        ];

        return view('/pengetahuan/index', $data);
    }

    public function get_pdf_for_chat($id)
    {
        try {
            $model = new PengetahuanModel();
            $pengetahuan = $model->find($id);

            if (!$pengetahuan) {
                log_message('error', 'PDF not found for chat - ID: ' . $id);
                return $this->response->setJSON([
                    'error' => 'Dokumen tidak ditemukan',
                    'server_time' => date('Y-m-d H:i:s')
                ])->setStatusCode(404);
            }

            $pdfPath = WRITEPATH . '../public/assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan'];

            if (!file_exists($pdfPath)) {
                log_message('error', 'PDF file missing - Path: ' . $pdfPath);
                return $this->response->setJSON([
                    'error' => 'File PDF tidak ditemukan di server',
                    'server_time' => date('Y-m-d H:i:s')
                ])->setStatusCode(404);
            }

            log_message('info', 'PDF ready for chat - ID: ' . $id);
            return $this->response->setJSON([
                'success' => true,
                'pdf_url' => base_url('assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan']),
                'file_size' => round(filesize($pdfPath) / 1024 / 1024, 2) . ' MB',
                'server_time' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            log_message('critical', 'Error in get_pdf_for_chat: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => 'Terjadi kesalahan internal',
                'detail' => $e->getMessage(),
                'server_time' => date('Y-m-d H:i:s')
            ])->setStatusCode(500);
        }
    }


    public function view($id)
    {
        $pengetahuan = $this->pengetahuanModel->getPengetahuanWithUserById($id);

        // Jika data tidak ditemukan atau akses tidak publik
        if (!$pengetahuan || $pengetahuan['akses_publik'] != 1) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Pengetahuan dengan ID $id tidak ditemukan atau tidak tersedia untuk publik.");
        }

        // Ambil pengetahuan lain yang publik dan bukan yang sedang dibuka
        $pengetahuanLain = $this->pengetahuanModel
            ->where('id !=', $id)
            ->where('akses_publik', 1)
            ->orderBy('created_at', 'DESC')
            ->findAll(6); // Ambil maksimal 6 pengetahuan lain

        $data = [
            'title' => $pengetahuan['judul'],
            'pengetahuan' => $pengetahuan,
            'pengetahuan_lain' => $pengetahuanLain,
        ];

        // Tambahkan ini untuk mengambil komentar
        $komentarModel = new \App\Models\KomentarPengetahuanModel();
        $komentar = $komentarModel->getKomentarByPengetahuan($id);

        $data['komentar'] = $komentar;

        return view('pengetahuan/view', $data);
    }

    public function comment($pengetahuan_id)
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        // DEBUG: Tampilkan semua data POST
        log_message('debug', 'POST Data: ' . print_r($this->request->getPost(), true));

        $validation = \Config\Services::validation();
        $validation->setRules([
            'komentar' => 'required|min_length[3]|max_length[1000]',
            'parent_id' => 'permit_empty|integer'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            log_message('debug', 'Validation Errors: ' . print_r($validation->getErrors(), true));
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        if (!session()->get('logged_in') || !session()->get('id')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $komentarModel = new \App\Models\KomentarPengetahuanModel();

        // Ambil parent_id dan pastikan handling yang benar
        $parent_id = $this->request->getPost('parent_id');
        $level = 0;

        // Debug parent_id
        log_message('debug', 'Raw parent_id from POST: ' . var_export($parent_id, true));

        // Handle empty string atau nilai tidak valid
        if ($parent_id === '' || $parent_id === '0' || $parent_id === null) {
            $parent_id = null;
            log_message('debug', 'Parent_id set to null');
        } else {
            $parent_id = (int) $parent_id;
            log_message('debug', 'Parent_id converted to integer: ' . $parent_id);
        }

        if ($parent_id) {
            log_message('debug', 'Processing parent comment for ID: ' . $parent_id);
            $parentComment = $komentarModel->find($parent_id);
            log_message('debug', 'Parent comment found: ' . ($parentComment ? 'YES' : 'NO'));

            if ($parentComment) {
                $level = $parentComment['level'] + 1;
                log_message('debug', 'New level: ' . $level);
            }
        }

        $data = [
            'pengetahuan_id' => $pengetahuan_id,
            'user_id' => session()->get('id'),
            'parent_id' => $parent_id, // Bisa null atau integer
            'level' => $level,
            'komentar' => $this->request->getPost('komentar'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        log_message('debug', 'Final data to save: ' . print_r($data, true));

        try {
            if ($komentarModel->save($data)) {
                $insertID = $komentarModel->getInsertID();
                log_message('debug', 'Komentar berhasil disimpan dengan ID: ' . $insertID);

                // Verify data yang tersimpan
                $savedData = $komentarModel->find($insertID);
                log_message('debug', 'Data yang tersimpan di DB: ' . print_r($savedData, true));

                return redirect()->back()->with('message', 'Komentar berhasil ditambahkan');
            } else {
                $errors = $komentarModel->errors();
                log_message('error', 'Save errors: ' . print_r($errors, true));
                return redirect()->back()->with('error', 'Gagal menambahkan komentar');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    // app/Controllers/Pengetahuan.php
    public function deleteComment($comment_id)
    {
        // Pastikan user sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $komentarModel = new \App\Models\KomentarPengetahuanModel();
        $user_id = session()->get('id');

        // Cek hak akses
        if (!$komentarModel->canDelete($comment_id, $user_id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk menghapus komentar ini');
        }

        if ($komentarModel->delete($comment_id)) {
            return redirect()->back()->with('message', 'Komentar berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus komentar');
        }
    }



    // public function askPdf()
    // {
    //     $validation = \Config\Services::validation();
    //     $validation->setRules([
    //         'pdf_id' => 'required|numeric',
    //         'question' => 'required|string|max_length[500]'
    //     ]);

    //     if (!$validation->withRequest($this->request)->run()) {
    //         return $this->response->setJSON(['success' => false, 'message' => 'Invalid input']);
    //     }

    //     $pdfId = $this->request->getJsonVar('pdf_id');
    //     $question = $this->request->getJsonVar('question');

    //     try {
    //         $document = $this->pengetahuanModel->find($pdfId);
    //         if (!$document) {
    //             throw new \Exception("Document not found");
    //         }

    //         // $pdfPath = WRITEPATH . '../public/assets/uploads/pengetahuan/' . $document['file_pdf_pengetahuan'];
    //         // $pdfPath = WRITEPATH . '/assets/uploads/pengetahuan/' . $document['file_pdf_pengetahuan'];

    //         // Di controller
    //         $pdfPath = WRITEPATH . '../public/assets/uploads/pengetahuan/' . $document['file_pdf_pengetahuan'];

    //         if (!file_exists($pdfPath)) {
    //             return $this->response->setJSON([
    //                 'success' => false,
    //                 'message' => 'File PDF tidak ditemukan di server'
    //             ]);
    //         }

    //         $rag = new \App\Libraries\PdfRag();
    //         $result = $rag->processPdf($pdfPath, $question);

    //         if (!$result) {
    //             throw new \Exception("Failed to process PDF");
    //         }

    //         return $this->response->setJSON([
    //             'success' => true,
    //             'answer' => $result['answer'],
    //             'sources' => $result['sources'] ?? []
    //         ]);
    //     } catch (\Exception $e) {
    //         log_message('error', 'PDF RAG Error: ' . $e->getMessage());
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

    // public function askPdf()
    // {

    //     if (!$this->request->isAJAX()) {
    //         return $this->response->setStatusCode(405)->setJSON([
    //             'success' => false,
    //             'message' => 'Method not allowed'
    //         ]);
    //     }

    //     $pdfId = $this->request->getVar('pdf_id');
    //     $question = $this->request->getVar('question');

    //     $pengetahuan = $this->pengetahuanModel->find($pdfId);
    //     if (!$pengetahuan) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Dokumen tidak ditemukan'
    //         ]);
    //     }

    //     $pdfPath = WRITEPATH . '../public/assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan'];

    //     $ragService = new \App\Services\RagService();
    //     $result = $ragService->queryPdf($question, $pdfPath);

    //     return $this->response->setJSON([
    //         'success' => true,
    //         'answer' => $result['answer'] ?? 'No answer generated',
    //         'sources' => $result['sources'] ?? []
    //     ]);
    // }

    public function processPdf($id)
    {
        $pengetahuan = $this->pengetahuanModel->find($id);
        if (!$pengetahuan) {
            return $this->response->setJSON(['error' => 'Dokumen tidak ditemukan']);
        }

        $pdfPath = WRITEPATH . '../public/assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan'];

        if (!file_exists($pdfPath)) {
            return $this->response->setJSON(['error' => 'File PDF tidak ditemukan']);
        }

        // Kirim PDF ke Python service
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->post('http://localhost:5000/process_pdf', [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($pdfPath, 'r'),
                        'filename' => $pengetahuan['file_pdf_pengetahuan']
                    ]
                ],
                'timeout' => 60
            ]);

            $result = json_decode($response->getBody(), true);

            if (isset($result['error'])) {
                log_message('error', 'RAG processing error: ' . $result['error']);
                return $this->response->setJSON(['error' => 'Gagal memproses PDF: ' . $result['error']]);
            }

            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'RAG service error: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Service AI sedang tidak tersedia']);
        }
    }

    public function askPdf()
    {
        $pdfId = $this->request->getVar('pdf_id');
        $question = $this->request->getVar('question');

        $pengetahuan = $this->pengetahuanModel->find($pdfId);
        if (!$pengetahuan) {
            return $this->response->setJSON(['error' => 'Dokumen tidak ditemukan']);
        }

        $docId = $pengetahuan['file_pdf_pengetahuan'];
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->post('http://localhost:5000/query', [
                'json' => [
                    'question' => $question,
                    'doc_id' => $docId
                ],
                'timeout' => 30
            ]);

            return $this->response->setJSON(json_decode($response->getBody(), true));
        } catch (\Exception $e) {
            log_message('error', 'Query error: ' . $e->getMessage());
            return $this->response->setJSON([
                'answer' => 'Maaf, layanan pertanyaan sedang tidak tersedia',
                'error' => $e->getMessage()
            ]);
        }
    }
}
