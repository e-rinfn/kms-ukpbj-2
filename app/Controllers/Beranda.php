<?php

namespace App\Controllers;

use App\Models\PengetahuanModel;
use App\Models\PelatihanModel;
use App\Models\UserModel;
use App\Models\PengajuanModel;

class Beranda extends BaseController
{
    protected $pengetahuanModel;
    protected $pelatihanModel;
    protected $userModel;
    protected $pengajuanModel;

    public function __construct()
    {
        // Cek session login, jika tidak ada redirect ke halaman utama
        if (!session()->get('logged_in')) {
            header('Location: /'); // redirect manual (karena __construct belum bisa pakai redirect()->to())
            exit;
        }

        $this->pengetahuanModel = new PengetahuanModel();
        $this->pelatihanModel   = new PelatihanModel();
        $this->userModel        = new UserModel();
        $this->pengajuanModel   = new PengajuanModel();
    }

    /**
     * Menampilkan halaman beranda dengan data pengetahuan dan pelatihan terbaru.
     */
    public function index()
    {
        $data = [
            'title'              => 'Beranda',
            'pengetahuan'        => $this->pengetahuanModel->getPublicPengetahuan(5), // Ambil 5 terbaru
            'pelatihan'          => $this->pelatihanModel->getPublicPelatihan(5),     // Ambil 5 terbaru
            'total_pengetahuan'  => $this->pengetahuanModel->countAll(),
            'total_pelatihan'    => $this->pelatihanModel->countAll(),
            'total_pengguna'     => $this->userModel->countAllResults(),
            'total_pengajuan'    => $this->pengajuanModel->countAllResults(),
            'pengetahuan_terbaru' => $this->pengetahuanModel->orderBy('created_at', 'DESC')->findAll(5),
            'pelatihan_terbaru'  => $this->pelatihanModel->orderBy('created_at', 'DESC')->findAll(5),
            // 'aktivitas_terbaru' => $this->getAktivitasTerbaru(), // Jika ada
        ];

        return view('admin/beranda', $data);
    }
}
