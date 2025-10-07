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

    public function index()
    {
        $data = [
            'title' => 'Daftar Pelatihan',
            'pelatihan' => $this->pelatihanModel->getPelatihanWithUser(),
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
            return redirect()->to('/pegawai/pelatihan/create')->withInput();
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
        return redirect()->to('/pegawai/pelatihan');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Pelatihan',
            'validation' => \Config\Services::validation(),
            'pelatihan' => $this->pelatihanModel->getPelatihanWithUser($id)
        ];
        return view('pegawai/pelatihan/edit', $data);
    }

    public function update($id)
    {
        $pelatihan = $this->pelatihanModel->find($id);

        $rules = [
            'judul' => 'required'
        ];

        if ($this->request->getFile('video')->getError() != 4) {
            $rules['video'] = [
                'rules' => 'uploaded[video]|max_size[video,10240]|ext_in[video,mp4,mov,avi]',
                'errors' => [
                    'uploaded' => 'Pilih video terlebih dahulu',
                    'max_size' => 'Ukuran video maksimal 10MB',
                    'ext_in' => 'Format video harus mp4, mov, atau avi'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->to('/pegawai/pelatihan/edit/' . $id)->withInput();
        }

        $data = [
            'judul' => $this->request->getVar('judul'),
            'caption_pelatihan' => $this->request->getVar('caption'),
            'akses_publik' => $this->request->getVar('akses_publik') ? 1 : 0,
        ];

        // Update video jika ada
        $video = $this->request->getFile('video');
        if ($video->getError() != 4) {
            unlink('assets/uploads/pelatihan/' . $pelatihan['video_pelatihan']);
            $namaVideo = $video->getRandomName();
            $video->move('assets/uploads/pelatihan', $namaVideo);
            $data['video_pelatihan'] = $namaVideo;
        }

        $this->pelatihanModel->update($id, $data);

        session()->setFlashdata('pesan', 'Data berhasil diubah.');
        return redirect()->to('/pegawai/pelatihan');
    }

    public function delete($id)
    {
        $pelatihan = $this->pelatihanModel->find($id);

        // Hapus video
        unlink('assets/uploads/pelatihan/' . $pelatihan['video_pelatihan']);

        $this->pelatihanModel->delete($id);
        session()->setFlashdata('pesan', 'Data berhasil dihapus.');
        return redirect()->to('/pegawai/pelatihan');
    }

    public function view($id)
    {
        $data = [
            'title' => 'Detail Pelatihan',
            'pelatihan' => $this->pelatihanModel->getPelatihanWithUser($id)
        ];
        return view('pegawai/pelatihan/view', $data);
    }
}
