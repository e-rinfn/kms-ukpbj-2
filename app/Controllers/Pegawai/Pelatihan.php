<?php

namespace App\Controllers\Pegawai;

use App\Controllers\BaseController;
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

    // Di Controller (misal: PelatihanController.php)
    public function index()
    {
        $model = new PelatihanModel();

        // Ambil parameter pencarian
        $search = $this->request->getGet('search');
        $filterAkses = $this->request->getGet('akses');

        // Query dasar dengan join user
        $builder = $model->select('pelatihan.*, user.nama as user_nama')
            ->join('user', 'user.id = pelatihan.user_id');

        // Tambahkan kondisi pencarian jika ada
        if (!empty($search)) {
            $builder->groupStart()
                ->like('pelatihan.judul', $search)
                ->orLike('pelatihan.caption_pelatihan', $search)
                ->orLike('user.nama', $search)
                ->groupEnd();
        }

        // Tambahkan filter akses publik jika ada
        if ($filterAkses !== null && $filterAkses !== '') {
            $builder->where('pelatihan.akses_publik', $filterAkses);
        }

        // Pagination
        $data = [
            'pelatihan' => $builder->orderBy('pelatihan.created_at', 'DESC')->paginate(10),
            'pager' => $model->pager,
            'search' => $search,
            'filterAkses' => $filterAkses
        ];

        return view('pegawai/pelatihan/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Pelatihan',
            'validation' => \Config\Services::validation()
        ];
        return view('pegawai/pelatihan/create', $data);
    }

    public function save()
    {
        // Cek jika request adalah AJAX
        $isAjax = $this->request->isAJAX();

        // Validasi dasar yang selalu diperlukan
        $validationRules = [
            'judul' => 'required'
        ];

        // Cek apakah user memilih untuk upload video atau tidak
        $video = $this->request->getFile('video');
        $isUploadingVideo = $video && $video->isValid() && !$video->hasMoved();

        // Jika user memilih upload video, tambahkan validasi video
        if ($isUploadingVideo) {
            $validationRules['video'] = [
                'rules' => 'uploaded[video]|max_size[video,512000]|ext_in[video,mp4,mov,avi]',
                'errors' => [
                    'uploaded' => 'Pilih video terlebih dahulu',
                    'max_size' => 'Ukuran video maksimal 500MB',
                    'ext_in' => 'Format video harus mp4, mov, atau avi'
                ]
            ];
        }

        if (!$this->validate($validationRules)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->to('/pegawai/pelatihan/create')->withInput();
        }

        try {
            // Siapkan data dasar
            $data = [
                'judul' => $this->request->getVar('judul'),
                'link_youtube' => $this->request->getVar('link_youtube'),
                'caption_pelatihan' => $this->request->getVar('caption'),
                'akses_publik' => $this->request->getVar('akses_publik') ? 1 : 0,
                'user_id' => session()->get('id')
            ];

            // Upload video hanya jika user memilih untuk upload
            if ($isUploadingVideo) {
                $namaVideo = $video->getRandomName();
                $video->move('assets/uploads/pelatihan', $namaVideo);
                $data['video_pelatihan'] = $namaVideo;
            }

            $this->pelatihanModel->save($data);

            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data berhasil ditambahkan.'
                ]);
            }

            session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');
            return redirect()->to('/pegawai/pelatihan');
        } catch (\Exception $e) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }

            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->to('/pegawai/pelatihan/create')->withInput();
        }
    }

    public function edit($id)
    {
        $pelatihanModel = new \App\Models\PelatihanModel();
        $data = $pelatihanModel->getPelatihanWithUserById($id);

        if (!$data) {
            // Jika data tidak ditemukan, lempar 404
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Pengetahuan dengan ID $id tidak ditemukan.");
        }

        return view('pegawai/pelatihan/edit', [
            'pelatihan' => $data
        ]);
    }


    public function update($id)
    {
        $pelatihan = $this->pelatihanModel->find($id);

        if (!$pelatihan) {
            return redirect()->to('/pegawai/pelatihan')->with('error', 'Data pelatihan tidak ditemukan.');
        }

        $rules = [
            'judul'   => 'required'
        ];

        $uploadChoice = $this->request->getVar('upload_choice');
        $video        = $this->request->getFile('video');

        // Validasi video hanya jika pilihan upload video baru
        if ($uploadChoice === 'upload_video' && $video && $video->isValid() && !$video->hasMoved()) {
            $rules['video'] = [
                'rules' => 'max_size[video,512000]|ext_in[video,mp4,mov,avi]',
                'errors' => [
                    'max_size' => 'Ukuran video maksimal 500MB',
                    'ext_in'   => 'Format video harus mp4, mov, atau avi'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Validasi gagal',
                    'errors'  => $this->validator->getErrors()
                ]);
            }
            return redirect()->to('/pegawai/pelatihan/edit/' . $id)
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'judul'            => $this->request->getVar('judul'),
            'caption_pelatihan' => $this->request->getVar('caption'),
            'link_youtube'     => $this->request->getVar('link_youtube'),
            'akses_publik'     => $this->request->getVar('akses_publik') ? 1 : 0,
        ];

        // --- Logika berdasarkan upload_choice ---
        if ($uploadChoice === 'youtube_only') {
            // Hapus file lama jika ada
            if (!empty($pelatihan['video_pelatihan'])) {
                $videoLama = 'assets/uploads/pelatihan/' . $pelatihan['video_pelatihan'];
                if (file_exists($videoLama)) {
                    unlink($videoLama);
                }
            }
            $data['video_pelatihan'] = null;
        } elseif ($uploadChoice === 'upload_video' && $video && $video->isValid() && !$video->hasMoved()) {
            // Hapus file lama jika ada
            if (!empty($pelatihan['video_pelatihan'])) {
                $videoLama = 'assets/uploads/pelatihan/' . $pelatihan['video_pelatihan'];
                if (file_exists($videoLama)) {
                    unlink($videoLama);
                }
            }

            // Upload video baru
            $namaVideo = $video->getRandomName();
            $video->move('assets/uploads/pelatihan', $namaVideo);
            $data['video_pelatihan'] = $namaVideo;
        }
        // else keep_existing → tidak melakukan perubahan pada video

        $this->pelatihanModel->update($id, $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data berhasil diubah.'
            ]);
        }

        session()->setFlashdata('pesan', 'Data berhasil diubah.');
        return redirect()->to('/pegawai/pelatihan');
    }

    public function delete($id)
    {
        $pelatihan = $this->pelatihanModel->find($id);
        $videoPath = 'assets/uploads/pelatihan/' . $pelatihan['video_pelatihan'];

        if (!empty($pelatihan['video_pelatihan']) && file_exists($videoPath)) {
            unlink($videoPath);
        }

        $this->pelatihanModel->delete($id);
        session()->setFlashdata('pesan', 'Data berhasil dihapus.');
        return redirect()->to('/pegawai/pelatihan');
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
        return view('pegawai/pelatihan/view', [
            'pelatihan'      => $pelatihan,
            'pelatihan_lain' => $pelatihanLain,
            'komentar'         => $komentar
        ]);
    }
}
