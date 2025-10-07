<?php

namespace App\Controllers;

use App\Models\PengetahuanModel;
use App\Models\PelatihanModel;

class Home extends BaseController
{
    protected $pengetahuanModel;
    protected $pelatihanModel;

    public function __construct()
    {
        $this->pengetahuanModel = new PengetahuanModel();
        $this->pelatihanModel = new PelatihanModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Home',
            'pengetahuan' => $this->pengetahuanModel->getPublicPengetahuan(5), // Ambil 5 terbaru
            'pelatihan' => $this->pelatihanModel->getPublicPelatihan(5) // Ambil 5 terbaru
        ];

        return view('home', $data);
    }
}
