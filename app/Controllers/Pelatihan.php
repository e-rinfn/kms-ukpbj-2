<?php

namespace App\Controllers;

use App\Models\PelatihanModel;
use App\Models\UserModel;

class Pelatihan extends BaseController
{
    protected $pelatihanModel;
    protected $userModel;

    public function __construct()
    {
        $this->pelatihanModel = new PelatihanModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('q'); // Menggunakan 'q' sesuai dengan form

        $data = [
            'title' => 'Daftar Pelatihan',
            'pelatihan' => $this->pelatihanModel->getPelatihanWithSearch($keyword),
            'pager' => $this->pelatihanModel->pager,
            'keyword' => $keyword, // Menggunakan 'keyword' untuk konsistensi
        ];

        return view('/pelatihan/index', $data);
    }

    public function view($id)
    {
        $pelatihanModel = new PelatihanModel();
        $komentarModel    = new \App\Models\KomentarPelatihanModel();

        // Ambil data pelatihan utama
        $pelatihan = $pelatihanModel->getPelatihanWithUserById($id);

        if (!$pelatihan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Pelatihan dengan ID $id tidak ditemukan"
            );
        }

        // Ambil pelatihan lain selain ID ini (maksimal 5 data)
        $pelatihanLain = $pelatihanModel
            ->where('id !=', $id)
            ->findAll(5);

        // Ambil komentar terkait
        $komentar = $komentarModel->getKomentarByPelatihan($id);

        // Kirim semua data ke view
        return view('/pelatihan/view', [
            'pelatihan'      => $pelatihan,
            'pelatihan_lain' => $pelatihanLain,
            'komentar'         => $komentar
        ]);
    }


    public function comment($pelatihan_id)
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

        $komentarModel = new \App\Models\KomentarPelatihanModel();

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
            'pelatihan_id' => $pelatihan_id,
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

    // app/Controllers/Pelatihan.php
    public function deleteComment($comment_id)
    {
        // Pastikan user sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $komentarModel = new \App\Models\KomentarPelatihanModel();
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
}
