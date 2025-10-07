<?php

namespace App\Controllers\Admin;

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

        return view('admin/pengetahuan/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Pengetahuan',
            'validation' => \Config\Services::validation()
        ];
        return view('admin/pengetahuan/create', $data);
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
                'rules' => 'uploaded[thumbnail]|max_size[thumbnail,1024]|is_image[thumbnail]|mime_in[thumbnail,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Pilih thumbnail terlebih dahulu',
                    'max_size' => 'Ukuran gambar maksimal 1MB',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'
                ]
            ]
        ])) {
            return redirect()->to('/admin/pengetahuan/create')->withInput()->with('validation', $this->validator);
        }


        // Upload file PDF
        $filePdf = $this->request->getFile('file_pdf');
        $namaPdf = $filePdf->getRandomName();
        $filePdf->move('assets/uploads/pengetahuan', $namaPdf);

        // Upload thumbnail
        $thumbnail = $this->request->getFile('thumbnail');
        $namaThumbnail = $thumbnail->getRandomName();
        $thumbnail->move('assets/uploads/pengetahuan', $namaThumbnail);

        // Simpan ke database
        $this->pengetahuanModel->save([
            'judul' => $this->request->getVar('judul'),
            'file_pdf_pengetahuan' => $namaPdf,
            'thumbnail_pengetahuan' => $namaThumbnail,
            'caption_pengetahuan' => $this->request->getVar('caption'),
            'akses_publik' => $this->request->getVar('akses_publik') ? 1 : 0,
            'user_id' => session()->get('id')
        ]);

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');
        return redirect()->to('/admin/pengetahuan');
    }


    public function edit($id)
    {
        $pengetahuanModel = new \App\Models\PengetahuanModel();
        $data = $pengetahuanModel->getPengetahuanWithUserById($id);

        if (!$data) {
            // Jika data tidak ditemukan, lempar 404
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Pengetahuan dengan ID $id tidak ditemukan.");
        }

        return view('admin/pengetahuan/edit', [
            'pengetahuan' => $data
        ]);
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
            return redirect()->to('/admin/pengetahuan/edit/' . $id)->withInput();
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
        return redirect()->to('/admin/pengetahuan');
    }

    // public function update($id)
    // {
    //     $pengetahuan = $this->pengetahuanModel->find($id);

    //     $rules = [
    //         'judul' => 'required'
    //     ];

    //     // Validasi file PDF jika di-upload
    //     if ($this->request->getFile('file_pdf')->getError() != 4) {
    //         $rules['file_pdf'] = [
    //             'rules' => 'uploaded[file_pdf]|max_size[file_pdf,5120]|ext_in[file_pdf,pdf]',
    //             'errors' => [
    //                 'uploaded' => 'Pilih file PDF terlebih dahulu',
    //                 'max_size' => 'Ukuran file maksimal 5MB',
    //                 'ext_in' => 'File harus berformat PDF'
    //             ]
    //         ];
    //     }

    //     // Validasi thumbnail jika di-upload
    //     if ($this->request->getFile('thumbnail')->getError() != 4) {
    //         $rules['thumbnail'] = [
    //             'rules' => 'max_size[thumbnail,1024]|is_image[thumbnail]|mime_in[thumbnail,image/jpg,image/jpeg,image/png]',
    //             'errors' => [
    //                 'max_size' => 'Ukuran gambar maksimal 1MB',
    //                 'is_image' => 'Yang anda pilih bukan gambar',
    //                 'mime_in' => 'Yang anda pilih bukan gambar'
    //             ]
    //         ];
    //     }

    //     if (!$this->validate($rules)) {
    //         return redirect()->to('/admin/pengetahuan/edit/' . $id)->withInput();
    //     }

    //     $data = [
    //         'judul' => $this->request->getVar('judul'),
    //         'caption_pengetahuan' => $this->request->getVar('caption'),
    //         'akses_publik' => $this->request->getVar('akses_publik') ? 1 : 0,
    //     ];

    //     // Update file PDF jika ada
    //     $filePdf = $this->request->getFile('file_pdf');
    //     if ($filePdf->getError() != 4) {
    //         $pdfLama = 'assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan'];
    //         if (!empty($pengetahuan['file_pdf_pengetahuan']) && file_exists($pdfLama)) {
    //             unlink($pdfLama);
    //         }

    //         $namaPdf = $filePdf->getRandomName();
    //         $filePdf->move('assets/uploads/pengetahuan', $namaPdf);
    //         $data['file_pdf_pengetahuan'] = $namaPdf;
    //     }

    //     // Update thumbnail jika ada
    //     $thumbnail = $this->request->getFile('thumbnail');
    //     if ($thumbnail->getError() != 4) {
    //         $thumbnailLama = 'assets/uploads/pengetahuan/' . $pengetahuan['thumbnail_pengetahuan'];
    //         if (
    //             !empty($pengetahuan['thumbnail_pengetahuan']) &&
    //             $pengetahuan['thumbnail_pengetahuan'] != 'default.jpg' &&
    //             file_exists($thumbnailLama)
    //         ) {
    //             unlink($thumbnailLama);
    //         }

    //         $namaThumbnail = $thumbnail->getRandomName();
    //         $thumbnail->move('assets/uploads/pengetahuan', $namaThumbnail);
    //         $data['thumbnail_pengetahuan'] = $namaThumbnail;
    //     }

    //     $this->pengetahuanModel->update($id, $data);

    //     session()->setFlashdata('pesan', 'Data berhasil diubah.');
    //     return redirect()->to('/admin/pengetahuan');
    // }


    // public function delete($id)
    // {
    //     $pengetahuan = $this->pengetahuanModel->find($id);

    //     // Hapus file PDF
    //     unlink('assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan']);

    //     // Hapus thumbnail jika bukan default
    //     if ($pengetahuan['thumbnail_pengetahuan'] != 'default.jpg') {
    //         unlink('assets/uploads/pengetahuan/' . $pengetahuan['thumbnail_pengetahuan']);
    //     }

    //     $this->pengetahuanModel->delete($id);
    //     session()->setFlashdata('pesan', 'Data berhasil dihapus.');
    //     return redirect()->to('/admin/pengetahuan');
    // }

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
        return redirect()->to('/admin/pengetahuan');
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
        return view('admin/pengetahuan/view', [
            'pengetahuan'      => $pengetahuan,
            'pengetahuan_lain' => $pengetahuanLain,
            'komentar'         => $komentar
        ]);
    }


    public function comment($pengetahuan_id)
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'komentar' => 'required|min_length[3]|max_length[1000]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $komentarModel = new \App\Models\KomentarPengetahuanModel();

        $data = [
            'pengetahuan_id' => $pengetahuan_id,
            'user_id' => session()->get('id'), // Pastikan user sudah login
            'komentar' => $this->request->getPost('komentar'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($komentarModel->addKomentar($data)) {
            return redirect()->back()->with('message', 'Komentar berhasil ditambahkan');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan komentar');
        }
    }
}
