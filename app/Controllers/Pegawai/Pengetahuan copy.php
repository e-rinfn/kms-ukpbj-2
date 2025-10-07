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
        $data = [
            'title' => 'Daftar Pengetahuan',
            'pengetahuan' => $this->pengetahuanModel->getPengetahuanWithUser(),
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
                'rules' => 'uploaded[file_pdf]|max_size[file_pdf,5120]|ext_in[file_pdf,pdf]',
                'errors' => [
                    'uploaded' => 'Pilih file PDF terlebih dahulu',
                    'max_size' => 'Ukuran file maksimal 5MB',
                    'ext_in' => 'File harus berformat PDF'
                ]
            ],
            'thumbnail' => [
                'rules' => 'max_size[thumbnail,1024]|is_image[thumbnail]|mime_in[thumbnail,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar maksimal 1MB',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'
                ]
            ]
        ])) {
            return redirect()->to('/pegawai/pengetahuan/create')->withInput();
        }

        // Upload file PDF
        $filePdf = $this->request->getFile('file_pdf');
        $namaPdf = $filePdf->getRandomName();
        $filePdf->move('assets/uploads/pengetahuan', $namaPdf);

        // Upload thumbnail jika ada
        $thumbnail = $this->request->getFile('thumbnail');
        $namaThumbnail = 'default.jpg';
        if ($thumbnail->getError() != 4) {
            $namaThumbnail = $thumbnail->getRandomName();
            $thumbnail->move('assets/uploads/pengetahuan', $namaThumbnail);
        }

        $this->pengetahuanModel->save([
            'judul' => $this->request->getVar('judul'),
            'file_pdf_pengetahuan' => $namaPdf,
            'thumbnail_pengetahuan' => $namaThumbnail,
            'caption_pengetahuan' => $this->request->getVar('caption'),
            'akses_publik' => $this->request->getVar('akses_publik') ? 1 : 0,
            'user_id' => session()->get('id')
        ]);

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');
        return redirect()->to('/pegawai/pengetahuan');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Pengetahuan',
            'validation' => \Config\Services::validation(),
            'pengetahuan' => $this->pengetahuanModel->getPengetahuanWithUser($id)
        ];
        return view('pegawai/pengetahuan/edit', $data);
    }

    public function update($id)
    {
        $pengetahuan = $this->pengetahuanModel->find($id);

        $rules = [
            'judul' => 'required'
        ];

        if ($this->request->getFile('file_pdf')->getError() != 4) {
            $rules['file_pdf'] = [
                'rules' => 'uploaded[file_pdf]|max_size[file_pdf,5120]|ext_in[file_pdf,pdf]',
                'errors' => [
                    'uploaded' => 'Pilih file PDF terlebih dahulu',
                    'max_size' => 'Ukuran file maksimal 5MB',
                    'ext_in' => 'File harus berformat PDF'
                ]
            ];
        }

        if ($this->request->getFile('thumbnail')->getError() != 4) {
            $rules['thumbnail'] = [
                'rules' => 'max_size[thumbnail,1024]|is_image[thumbnail]|mime_in[thumbnail,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar maksimal 1MB',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->to('/pegawai/pengetahuan/edit/' . $id)->withInput();
        }

        $data = [
            'judul' => $this->request->getVar('judul'),
            'caption_pengetahuan' => $this->request->getVar('caption'),
            'akses_publik' => $this->request->getVar('akses_publik') ? 1 : 0,
        ];

        // Update file PDF jika ada
        $filePdf = $this->request->getFile('file_pdf');
        if ($filePdf->getError() != 4) {
            unlink('assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan']);
            $namaPdf = $filePdf->getRandomName();
            $filePdf->move('assets/uploads/pengetahuan', $namaPdf);
            $data['file_pdf_pengetahuan'] = $namaPdf;
        }

        // Update thumbnail jika ada
        $thumbnail = $this->request->getFile('thumbnail');
        if ($thumbnail->getError() != 4) {
            if ($pengetahuan['thumbnail_pengetahuan'] != 'default.jpg') {
                unlink('assets/uploads/pengetahuan/' . $pengetahuan['thumbnail_pengetahuan']);
            }
            $namaThumbnail = $thumbnail->getRandomName();
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

        // Hapus file PDF
        unlink('assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan']);

        // Hapus thumbnail jika bukan default
        if ($pengetahuan['thumbnail_pengetahuan'] != 'default.jpg') {
            unlink('assets/uploads/pengetahuan/' . $pengetahuan['thumbnail_pengetahuan']);
        }

        $this->pengetahuanModel->delete($id);
        session()->setFlashdata('pesan', 'Data berhasil dihapus.');
        return redirect()->to('/pegawai/pengetahuan');
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

        return view('pengetahuan/view', $data);
    }
}
