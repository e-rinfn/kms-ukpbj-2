<?php

namespace App\Controllers;

use App\Models\PengetahuanModel;
use App\Models\PelatihanModel;
use App\Models\KomentarPengetahuanModel;
use App\Models\KomentarPelatihanModel;

class Chatbot extends BaseController
{
    protected $pengetahuanModel;
    protected $pelatihanModel;
    protected $komentarPengetahuanModel;
    protected $komentarPelatihanModel;

    public function __construct()
    {
        $this->pengetahuanModel = new PengetahuanModel();
        $this->pelatihanModel = new PelatihanModel();
        $this->komentarPengetahuanModel = new KomentarPengetahuanModel();
        $this->komentarPelatihanModel = new KomentarPelatihanModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Chatbot',
            'pengetahuan' => $this->pengetahuanModel->getPublicPengetahuan(),
            'pelatihan' => $this->pelatihanModel->getPublicPelatihan()
        ];
        return view('chatbot/index', $data);
    }

    public function process()
    {
        $input = $this->request->getVar('message');
        $response = $this->generateResponse($input);

        return $this->response->setJSON(['response' => $response]);
    }

    protected function generateResponse($input)
    {
        // Cari pengetahuan
        $pengetahuan = $this->pengetahuanModel->like('judul', $input)
            ->orLike('caption_pengetahuan', $input)
            ->where('akses_publik', 1)
            ->findAll();

        // Cari pelatihan
        $pelatihan = $this->pelatihanModel->like('judul', $input)
            ->orLike('caption_pelatihan', $input)
            ->where('akses_publik', 1)
            ->findAll();

        $response = '';

        if (!empty($pengetahuan)) {
            $response .= "Saya menemukan beberapa pengetahuan terkait:\n";
            foreach ($pengetahuan as $p) {
                $response .= "- <a href='/pengetahuan/view/{$p['id']}'>{$p['judul']}</a>\n";
            }
            $response .= "\n";
        }

        if (!empty($pelatihan)) {
            $response .= "Saya menemukan beberapa pelatihan terkait:\n";
            foreach ($pelatihan as $p) {
                $response .= "- <a href='/pelatihan/view/{$p['id']}'>{$p['judul']}</a>\n";
            }
            $response .= "\n";
        }

        if (empty($pengetahuan) && empty($pelatihan)) {
            $response = "Maaf, saya tidak menemukan informasi yang sesuai dengan pertanyaan Anda. Silakan coba dengan kata kunci yang berbeda.";
        }

        return $response;
    }

    public function addKomentarPengetahuan()
    {
        $data = [
            'pengetahuan_id' => $this->request->getVar('pengetahuan_id'),
            'user_id' => session()->get('id'),
            'komentar' => $this->request->getVar('komentar')
        ];

        $this->komentarPengetahuanModel->save($data);
        return redirect()->back();
    }

    public function addKomentarPelatihan()
    {
        $data = [
            'pelatihan_id' => $this->request->getVar('pelatihan_id'),
            'user_id' => session()->get('id'),
            'komentar' => $this->request->getVar('komentar')
        ];

        $this->komentarPelatihanModel->save($data);
        return redirect()->back();
    }
}
