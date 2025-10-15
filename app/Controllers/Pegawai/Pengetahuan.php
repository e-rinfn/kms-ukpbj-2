<?php

namespace App\Controllers\Pegawai;

use App\Controllers\BaseController;
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

    public function index()
    {
        $model = new PengetahuanModel();

        // Ambil parameter pencarian
        $search = $this->request->getGet('search');
        $filterAkses = $this->request->getGet('akses');

        // Query dasar dengan join user
        $builder = $model->select('pengetahuan.*, user.nama as user_nama')
            ->join('user', 'user.id = pengetahuan.user_id');

        // Tambahkan kondisi pencarian jika ada
        if (!empty($search)) {
            $builder->groupStart()
                ->like('pengetahuan.judul', $search)
                ->orLike('pengetahuan.caption_pengetahuan', $search)
                ->orLike('user.nama', $search)
                ->groupEnd();
        }

        // Tambahkan filter akses publik jika ada
        if ($filterAkses !== null && $filterAkses !== '') {
            $builder->where('pengetahuan.akses_publik', $filterAkses);
        }

        // Pagination
        $data = [
            'pengetahuan' => $builder->orderBy('pengetahuan.created_at', 'DESC')->paginate(10),
            'pager' => $model->pager,
            'search' => $search,
            'filterAkses' => $filterAkses
        ];

        return view('pegawai/pengetahuan/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Pengetahuan',
            'validation' => \Config\Services::validation()
        ];
        return view('pegawai/pengetahuan/create', $data);
    }

    public function save()
    {
        if (!$this->validate([
            'judul' => 'required',
            'file_pdf' => [
                'rules' => 'uploaded[file_pdf]|max_size[file_pdf,51200]|ext_in[file_pdf,pdf]',
                'errors' => [
                    'uploaded' => 'Pilih file PDF terlebih dahulu',
                    'max_size' => 'Ukuran file maksimal 50MB',
                    'ext_in'   => 'File harus berformat PDF'
                ]
            ],
            'thumbnail' => [
                // Tidak wajib upload, tapi jika ada harus valid
                'rules' => 'if_exist|max_size[thumbnail,1024]|is_image[thumbnail]|mime_in[thumbnail,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar maksimal 1MB',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in'  => 'Yang anda pilih bukan gambar'
                ]
            ]
        ])) {
            return redirect()->to('/pegawai/pengetahuan/create')
                ->withInput()
                ->with('validation', $this->validator);
        }

        // Upload file PDF (wajib)
        $filePdf = $this->request->getFile('file_pdf');
        $originalPdfName = pathinfo($filePdf->getClientName(), PATHINFO_FILENAME);
        $extensionPdf    = $filePdf->getClientExtension();
        $slugPdfName     = url_title($originalPdfName, '_', true);
        $namaPdf         = rand(1000, 9999) . '_' . $slugPdfName . '.' . $extensionPdf;
        $filePdf->move('assets/uploads/pengetahuan', $namaPdf);

        // Upload thumbnail (opsional)
        $namaThumbnail = null;
        $thumbnail = $this->request->getFile('thumbnail');
        if ($thumbnail && $thumbnail->isValid() && !$thumbnail->hasMoved()) {
            $originalThumbName = pathinfo($thumbnail->getClientName(), PATHINFO_FILENAME);
            $extensionThumb    = $thumbnail->getClientExtension();
            $slugThumbName     = url_title($originalThumbName, '_', true);
            $namaThumbnail     = rand(1000, 9999) . '_' . $slugThumbName . '.' . $extensionThumb;
            $thumbnail->move('assets/uploads/pengetahuan', $namaThumbnail);
        }

        // Simpan ke database
        $this->pengetahuanModel->save([
            'judul'                => $this->request->getVar('judul'),
            'file_pdf_pengetahuan' => $namaPdf,
            'thumbnail_pengetahuan' => $namaThumbnail, // bisa null
            'caption_pengetahuan'  => $this->request->getVar('caption'),
            'akses_publik'         => $this->request->getVar('akses_publik') ? 1 : 0,
            'user_id'              => session()->get('id')
        ]);

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');
        return redirect()->to('/pegawai/pengetahuan');
    }



    public function edit($id)
    {
        $pengetahuanModel = new \App\Models\PengetahuanModel();
        $data = $pengetahuanModel->getPengetahuanWithUserById($id);

        if (!$data) {
            // Jika data tidak ditemukan, lempar 404
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Pengetahuan dengan ID $id tidak ditemukan.");
        }

        return view('pegawai/pengetahuan/edit', [
            'pengetahuan' => $data
        ]);
    }


    public function update($id)
    {
        $pengetahuan = $this->pengetahuanModel->find($id);

        // Validasi
        $rules = [
            'judul' => 'required'
        ];

        // Validasi file PDF - PERBAIKAN: selalu tambahkan validasi untuk file PDF
        $filePdf = $this->request->getFile('file_pdf');
        if ($filePdf->getError() != 4) { // Error 4 berarti tidak ada file yang diupload
            $rules['file_pdf'] = [
                'rules' => 'uploaded[file_pdf]|max_size[file_pdf,51200]|ext_in[file_pdf,pdf]',
                'errors' => [
                    'uploaded' => 'Pilih file PDF terlebih dahulu',
                    'max_size' => 'Ukuran file maksimal 5MB',
                    'ext_in'   => 'File harus berformat PDF'
                ]
            ];
        }

        // Validasi thumbnail - PERBAIKAN: gunakan getError() != 4
        $thumbnail = $this->request->getFile('thumbnail');
        if ($thumbnail->getError() != 4) {
            $rules['thumbnail'] = [
                'rules' => 'max_size[thumbnail,1024]|is_image[thumbnail]|mime_in[thumbnail,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar maksimal 1MB',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in'  => 'Yang anda pilih bukan gambar'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->to('/pegawai/pengetahuan/edit/' . $id)->withInput();
        }

        $data = [
            'judul'              => $this->request->getVar('judul'),
            'caption_pengetahuan' => $this->request->getVar('caption'),
            'akses_publik'       => $this->request->getVar('akses_publik') ? 1 : 0,
        ];

        // Update PDF - PERBAIKAN: gunakan getError() != 4
        if ($filePdf->getError() != 4 && $filePdf->isValid() && !$filePdf->hasMoved()) {
            // hapus file lama
            if (!empty($pengetahuan['file_pdf_pengetahuan']) && file_exists('assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan'])) {
                unlink('assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan']);
            }
            // simpan file baru
            $namaPdf = rand(1000, 9999) . '_' . $filePdf->getClientName();
            $filePdf->move('assets/uploads/pengetahuan', $namaPdf);
            $data['file_pdf_pengetahuan'] = $namaPdf;
        }

        // Update Thumbnail - PERBAIKAN: gunakan getError() != 4
        if ($thumbnail->getError() != 4 && $thumbnail->isValid() && !$thumbnail->hasMoved()) {
            if (!empty($pengetahuan['thumbnail_pengetahuan']) && $pengetahuan['thumbnail_pengetahuan'] != 'default.jpg' && file_exists('assets/uploads/pengetahuan/' . $pengetahuan['thumbnail_pengetahuan'])) {
                unlink('assets/uploads/pengetahuan/' . $pengetahuan['thumbnail_pengetahuan']);
            }
            $namaThumbnail = rand(1000, 9999) . '_' . $thumbnail->getClientName();
            $thumbnail->move('assets/uploads/pengetahuan', $namaThumbnail);
            $data['thumbnail_pengetahuan'] = $namaThumbnail;
        }

        $this->pengetahuanModel->update($id, $data);

        session()->setFlashdata('pesan', 'Data berhasil diubah.');
        return redirect()->to('/pegawai/pengetahuan');
    }

    public function delete($id)
    {
        $pengetahuan = $this->pengetahuanModel->find($id);

        // Hapus file PDF jika ada
        $pdfPath = 'assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan'];
        if (!empty($pengetahuan['file_pdf_pengetahuan']) && file_exists($pdfPath)) {
            unlink($pdfPath);
        }

        // Hapus thumbnail jika bukan default dan file-nya ada
        $thumbnailPath = 'assets/uploads/pengetahuan/' . $pengetahuan['thumbnail_pengetahuan'];
        if (
            !empty($pengetahuan['thumbnail_pengetahuan']) &&
            $pengetahuan['thumbnail_pengetahuan'] != 'default.jpg' &&
            file_exists($thumbnailPath)
        ) {
            unlink($thumbnailPath);
        }

        // Hapus data dari database
        $this->pengetahuanModel->delete($id);

        session()->setFlashdata('pesan', 'Data berhasil dihapus.');
        return redirect()->to('/pegawai/pengetahuan');
    }

    public function view($id)
    {
        $pengetahuanModel = new PengetahuanModel();
        $komentarModel    = new \App\Models\KomentarPengetahuanModel();

        // Ambil data pengetahuan utama
        $pengetahuan = $pengetahuanModel->getPengetahuanWithUserById($id);

        if (!$pengetahuan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Pengetahuan dengan ID $id tidak ditemukan"
            );
        }

        // Ambil pengetahuan lain selain ID ini (maksimal 5 data)
        $pengetahuanLain = $pengetahuanModel
            ->where('id !=', $id)
            ->findAll(5);

        // Ambil komentar terkait
        $komentar = $komentarModel->getKomentarByPengetahuan($id);

        // Kirim semua data ke view
        return view('pegawai/pengetahuan/view', [
            'pengetahuan'      => $pengetahuan,
            'pengetahuan_lain' => $pengetahuanLain,
            'komentar'         => $komentar
        ]);
    }

    public function get_pdf_for_chat($id)
    {
        // Hanya response JSON untuk API
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $pengetahuan = $this->pengetahuanModel->find($id);
        if (!$pengetahuan || empty($pengetahuan['file_pdf_pengetahuan'])) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'PDF not found']);
        }

        $pdfPath = WRITEPATH . '../public/assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan'];
        if (!file_exists($pdfPath)) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'PDF file not found on server']);
        }

        $fileSize = filesize($pdfPath);
        $fileSizeFormatted = $this->formatBytes($fileSize);

        return $this->response->setJSON([
            'pdf_url' => base_url('assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan']),
            'file_size' => $fileSizeFormatted,
            'status' => 'success'
        ]);
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    // app/Controllers/Pengetahuan.php
    public function comment($pengetahuan_id)
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'komentar' => 'required|min_length[3]|max_length[1000]',
            'parent_id' => 'permit_empty|integer'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        if (!session()->get('logged_in') || !session()->get('id')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $komentarModel = new \App\Models\KomentarPengetahuanModel();

        $parent_id = $this->request->getPost('parent_id');
        $level = 0;

        if ($parent_id) {
            $parentComment = $komentarModel->find($parent_id);
            $level = $parentComment ? ($parentComment['level'] + 1) : 0;
        }

        $data = [
            'pengetahuan_id' => $pengetahuan_id,
            'user_id' => session()->get('id'),
            'parent_id' => $parent_id,
            'level' => $level,
            'komentar' => $this->request->getPost('komentar'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        // GUNAKAN save() BUKAN addKomentar()
        if ($komentarModel->save($data)) {
            return redirect()->back()->with('message', 'Komentar berhasil ditambahkan');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan komentar');
        }
    }


    // app/Controllers/Pengetahuan.php

    public function ask()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'error' => 'Method not allowed'
            ]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'pengetahuan_id' => 'required|numeric',
            'question' => 'required|string|max_length=500',
            'pdf_url' => 'required|valid_url'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Invalid input'
            ]);
        }

        $pengetahuanId = $this->request->getPost('pengetahuan_id');
        $question = $this->request->getPost('question');
        $pdfUrl = $this->request->getPost('pdf_url');

        try {
            // Pilih salah satu metode berikut:
            // 1. Untuk implementasi lokal dengan Llama 2
            $answer = $this->askWithLlama($pdfUrl, $question);

            // atau 2. Untuk implementasi dengan layanan eksternal
            // $answer = $this->askWithExternalService($pdfUrl, $question);

            return $this->response->setJSON([
                'answer' => $answer['response'],
                'source' => $answer['source'] ?? 'Dokumen ini'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error processing question: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Terjadi kesalahan saat memproses pertanyaan'
            ]);
        }
    }

    private function askWithLlama($pdfUrl, $question)
    {
        // Path ke file PDF yang sudah di-download
        $pdfPath = WRITEPATH . 'temp/' . basename($pdfUrl);

        // 1. Download PDF jika belum ada
        if (!file_exists($pdfPath)) {
            file_put_contents($pdfPath, file_get_contents($pdfUrl));
        }

        // 2. Ekstrak teks dari PDF
        $text = $this->extractTextFromPdf($pdfPath);

        // 3. Simpan teks ke file sementara
        $textPath = WRITEPATH . 'temp/document_text.txt';
        file_put_contents($textPath, $text);

        // 4. Panggil Llama 2 dengan teks sebagai konteks
        $command = 'llama.cpp/main -m models/llama-2-7b-chat.ggmlv3.q4_0.bin ' .
            '--color -f "' . $textPath . '" -p "' . $question . '"';

        $output = shell_exec($command);

        return [
            'response' => $output,
            'source' => 'Dokumen ini'
        ];
    }

    private function extractTextFromPdf($pdfPath)
    {
        // Install package PDF parser terlebih dahulu: composer require smalot/pdfparser
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($pdfPath);

        return $pdf->getText();
    }

    public function chat($id)
    {
        $pengetahuan = $this->pengetahuanModel->find($id);
        if (!$pengetahuan) {
            return redirect()->back()->with('error', 'Pengetahuan tidak ditemukan');
        }

        $data = [
            'pengetahuan' => $pengetahuan,
            'isLoggedIn' => session()->get('logged_in') === true,
            'user_id' => session()->get('id')
        ];

        return view('pengetahuan/view', $data);
    }

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

        // GUNAKAN delete() BUKAN method custom
        if ($komentarModel->delete($comment_id)) {
            return redirect()->back()->with('message', 'Komentar berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus komentar');
        }
    }
}
