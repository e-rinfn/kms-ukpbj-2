<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TemplateModel;
use App\Models\UserModel;

class Template extends BaseController
{
    protected $templateModel;
    protected $userModel;

    public function __construct()
    {
        $this->templateModel = new TemplateModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $model = new TemplateModel();

        // Ambil parameter pencarian
        $search = $this->request->getGet('search');
        $filterAkses = $this->request->getGet('akses');

        // Query dasar dengan join user
        $builder = $model->select('template.*, user.nama as user_nama')
            ->join('user', 'user.id = template.user_id');

        // Tambahkan kondisi pencarian jika ada
        if (!empty($search)) {
            $builder->groupStart()
                ->like('template.judul', $search)
                ->orLike('user.nama', $search)
                ->groupEnd();
        }

        // Tambahkan filter akses publik jika ada
        if ($filterAkses !== null && $filterAkses !== '') {
            $builder->where('template.akses_publik', $filterAkses);
        }

        // Pagination
        $data = [
            'template' => $builder->orderBy('template.created_at', 'DESC')->paginate(10),
            'pager' => $model->pager,
            'search' => $search,
            'filterAkses' => $filterAkses
        ];

        return view('admin/template/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Template',
            'validation' => \Config\Services::validation()
        ];
        return view('admin/template/create', $data);
    }

    public function save()
    {
        if (!$this->validate([
            'judul' => 'required',
            'file_docx' => [
                'rules' => 'uploaded[file_docx]|max_size[file_docx,51200]|ext_in[file_docx,docx]',
                'errors' => [
                    'uploaded' => 'Pilih file DOCX terlebih dahulu',
                    'max_size' => 'Ukuran file maksimal 50MB',
                    'ext_in' => 'File harus berformat DOCX'
                ]
            ]
        ])) {
            return redirect()->to('/admin/template/create')->withInput()->with('validation', $this->validator);
        }


        // Upload file DOCX
        $fileDocx = $this->request->getFile('file_docx');

        // Ambil nama asli file
        $originalName = $fileDocx->getName();

        // Bersihkan nama file (biar aman)
        $cleanName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $originalName);

        // Tambahkan angka unik di depan
        $namaDocx = time() . '_' . $cleanName;

        // Pindahkan file ke folder tujuan
        $fileDocx->move('assets/uploads/template', $namaDocx);

        // Simpan ke database
        $this->templateModel->save([
            'judul'        => $this->request->getVar('judul'),
            'file_docx'    => $namaDocx,
            'akses_publik' => $this->request->getVar('akses_publik') ? 1 : 0,
            'user_id'      => session()->get('id')
        ]);


        session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');
        return redirect()->to('/admin/template');
    }


    public function edit($id)
    {
        $templateModel = new \App\Models\TemplateModel();
        $data = $templateModel->getTemplateWithUserById($id);

        if (!$data) {
            // Jika data tidak ditemukan, lempar 404
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Template dengan ID $id tidak ditemukan.");
        }

        return view('admin/template/edit', [
            'template' => $data
        ]);
    }


    public function update($id)
    {
        $template = $this->templateModel->find($id);

        $rules = [
            'judul' => 'required'
        ];

        if ($this->request->getFile('file_docx')->getError() != 4) {
            $rules['file_docx'] = [
                'rules' => 'uploaded[file_docx]|max_size[file_docx,51200]|ext_in[file_docx,docx]',
                'errors' => [
                    'uploaded' => 'Pilih file PDF terlebih dahulu',
                    'max_size' => 'Ukuran file maksimal 50MB',
                    'ext_in' => 'File harus berformat PDF'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/template/edit/' . $id)->withInput();
        }

        $data = [
            'judul' => $this->request->getVar('judul'),
            'akses_publik' => $this->request->getVar('akses_publik') ? 1 : 0,
        ];

        // Update file DOCX jika ada
        $fileDocx = $this->request->getFile('file_docx');
        if ($fileDocx->getError() != 4) {
            unlink('assets/uploads/template/' . $template['file_docx']);
            $namaDocx = $fileDocx->getRandomName();
            $fileDocx->move('assets/uploads/template', $namaDocx);
            $data['file_docx'] = $namaDocx;
        }

        $this->templateModel->update($id, $data);

        session()->setFlashdata('pesan', 'Data berhasil diubah.');
        return redirect()->to('/admin/template');
    }

    public function delete($id)
    {
        $template = $this->templateModel->find($id);

        // Hapus file DOCX jika ada
        $docxPath = 'assets/uploads/template/' . $template['file_docx'];
        if (!empty($template['file_docx']) && file_exists($docxPath)) {
            unlink($docxPath);
        }

        // Hapus data dari database
        $this->templateModel->delete($id);

        session()->setFlashdata('pesan', 'Data berhasil dihapus.');
        return redirect()->to('/admin/template');
    }



    public function view($id)
    {
        $templateModel = new TemplateModel();

        // Ambil data template utama
        $template = $templateModel->getTemplateWithUserById($id);

        if (!$template) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Template dengan ID $id tidak ditemukan"
            );
        }

        // Ambil template lain selain ID ini (maksimal 5 data)
        $templateLain = $templateModel
            ->where('id !=', $id)
            ->findAll(5);

        // Ambil komentar terkait

        // Kirim semua data ke view
        return view('admin/template/view', [
            'template'      => $template,
            'template_lain' => $templateLain,
        ]);
    }

    public function get_pdf_for_chat($id)
    {
        // Hanya response JSON untuk API
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $template = $this->templateModel->find($id);
        if (!$template || empty($template['file_docx'])) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'DOCX not found']);
        }

        $docxPath = WRITEPATH . '../public/assets/uploads/template/' . $template['file_docx'];
        if (!file_exists($docxPath)) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'DOCX file not found on server']);
        }

        $fileSize = filesize($docxPath);
        $fileSizeFormatted = $this->formatBytes($fileSize);

        return $this->response->setJSON([
            'docx_url' => base_url('assets/uploads/template/' . $template['file_docx']),
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
}
