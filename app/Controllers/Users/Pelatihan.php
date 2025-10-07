<?php

namespace App\Controllers\Users;

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

        return view('users/pelatihan/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Pelatihan',
            'validation' => \Config\Services::validation()
        ];
        return view('users/pelatihan/create', $data);
    }

    public function save()
    {
        if (!$this->validate([
            'judul' => 'required',
            'video' => [
                'rules' => 'uploaded[video]|max_size[video,10240]|ext_in[video,mp4,mov,avi]',
                'errors' => [
                    'uploaded' => 'Pilih video terlebih dahulu',
                    'max_size' => 'Ukuran video maksimal 10MB',
                    'ext_in' => 'Format video harus mp4, mov, atau avi'
                ]
            ]
        ])) {
            return redirect()->to('/users/pelatihan/create')->withInput();
        }

        // Upload video
        $video = $this->request->getFile('video');
        $namaVideo = $video->getRandomName();
        $video->move('assets/uploads/pelatihan', $namaVideo);

        $this->pelatihanModel->save([
            'judul' => $this->request->getVar('judul'),
            'video_pelatihan' => $namaVideo,
            'caption_pelatihan' => $this->request->getVar('caption'),
            'akses_publik' => $this->request->getVar('akses_publik') ? 1 : 0,
            'user_id' => session()->get('id')
        ]);

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');
        return redirect()->to('/users/pelatihan');
    }

    public function edit($id)
    {
        $pelatihanModel = new \App\Models\PelatihanModel();
        $data = $pelatihanModel->getPelatihanWithUserById($id);

        if (!$data) {
            // Jika data tidak ditemukan, lempar 404
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Pengetahuan dengan ID $id tidak ditemukan.");
        }

        return view('users/pelatihan/edit', [
            'pelatihan' => $data
        ]);
    }

    // public function update($id)
    // {
    //     $pelatihan = $this->pelatihanModel->find($id);

    //     $rules = [
    //         'judul' => 'required'
    //     ];

    //     if ($this->request->getFile('video')->getError() != 4) {
    //         $rules['video'] = [
    //             'rules' => 'uploaded[video]|max_size[video,10240]|ext_in[video,mp4,mov,avi]',
    //             'errors' => [
    //                 'uploaded' => 'Pilih video terlebih dahulu',
    //                 'max_size' => 'Ukuran video maksimal 10MB',
    //                 'ext_in' => 'Format video harus mp4, mov, atau avi'
    //             ]
    //         ];
    //     }

    //     if (!$this->validate($rules)) {
    //         return redirect()->to('/users/pelatihan/edit/' . $id)->withInput();
    //     }

    //     $data = [
    //         'judul' => $this->request->getVar('judul'),
    //         'caption_pelatihan' => $this->request->getVar('caption'),
    //         'akses_publik' => $this->request->getVar('akses_publik') ? 1 : 0,
    //     ];

    //     // Update video jika ada
    //     $video = $this->request->getFile('video');
    //     if ($video->getError() != 4) {
    //         unlink('assets/uploads/pelatihan/' . $pelatihan['video_pelatihan']);
    //         $namaVideo = $video->getRandomName();
    //         $video->move('assets/uploads/pelatihan', $namaVideo);
    //         $data['video_pelatihan'] = $namaVideo;
    //     }

    //     $this->pelatihanModel->update($id, $data);

    //     session()->setFlashdata('pesan', 'Data berhasil diubah.');
    //     return redirect()->to('/users/pelatihan');
    // }

    public function update($id)
    {
        $pelatihan = $this->pelatihanModel->find($id);

        $rules = [
            'judul' => 'required'
        ];

        if ($this->request->getFile('video')->getError() != 4) {
            $rules['video'] = [
                'rules' => 'uploaded[video]|max_size[video,1024000]|ext_in[video,mp4,mov,avi]',
                'errors' => [
                    'uploaded' => 'Pilih video terlebih dahulu',
                    'max_size' => 'Ukuran video maksimal 1GB',
                    'ext_in' => 'Format video harus mp4, mov, atau avi'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->to('/users/pelatihan/edit/' . $id)->withInput();
        }

        $data = [
            'judul' => $this->request->getVar('judul'),
            'caption_pelatihan' => $this->request->getVar('caption'),
            'akses_publik' => $this->request->getVar('akses_publik') ? 1 : 0,
        ];

        // Update video jika ada
        $video = $this->request->getFile('video');
        if ($video->getError() != 4) {
            // Hapus video lama jika ada
            $videoLama = 'assets/uploads/pelatihan/' . $pelatihan['video_pelatihan'];
            if (!empty($pelatihan['video_pelatihan']) && file_exists($videoLama)) {
                unlink($videoLama);
            }

            // Upload video baru
            $namaVideo = $video->getRandomName();
            $video->move('assets/uploads/pelatihan', $namaVideo);
            $data['video_pelatihan'] = $namaVideo;
        }

        $this->pelatihanModel->update($id, $data);

        session()->setFlashdata('pesan', 'Data berhasil diubah.');
        return redirect()->to('/users/pelatihan');
    }


    // public function delete($id)
    // {
    //     $pelatihan = $this->pelatihanModel->find($id);

    //     // Hapus video
    //     unlink('assets/uploads/pelatihan/' . $pelatihan['video_pelatihan']);

    //     $this->pelatihanModel->delete($id);
    //     session()->setFlashdata('pesan', 'Data berhasil dihapus.');
    //     return redirect()->to('/users/pelatihan');
    // }

    public function delete($id)
    {
        $pelatihan = $this->pelatihanModel->find($id);
        $videoPath = 'assets/uploads/pelatihan/' . $pelatihan['video_pelatihan'];

        if (!empty($pelatihan['video_pelatihan']) && file_exists($videoPath)) {
            unlink($videoPath);
        }

        $this->pelatihanModel->delete($id);
        session()->setFlashdata('pesan', 'Data berhasil dihapus.');
        return redirect()->to('/users/pelatihan');
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
        return view('users/pelatihan/view', [
            'pelatihan'      => $pelatihan,
            'pelatihan_lain' => $pelatihanLain,
            'komentar'         => $komentar
        ]);
    }
}
