<?php

namespace App\Controllers;

use App\Models\PengajuanModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Pengajuan extends BaseController
{
    protected $pengajuanModel;
    protected $userModel;

    public function __construct()
    {
        $this->pengajuanModel = new PengajuanModel();
        $this->userModel = new UserModel();
    }

    // Halaman form pengajuan untuk pengguna
    public function index()
    {
        return view('/form_pengajuan');
    }

    // Proses pengajuan
    public function prosesPengajuan()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'nama' => 'required|min_length[3]',
            'nik' => 'required|numeric|min_length[16]|max_length[16]',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'no_hp' => 'required',
            'unit_kerja' => 'required',
            'file_pengajuan' => 'uploaded[file_pengajuan]|max_size[file_pengajuan,2048]|ext_in[file_pengajuan,pdf,jpg,jpeg,png]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Upload file
        $file = $this->request->getFile('file_pengajuan');
        $fileName = $file->getRandomName();
        $file->move(WRITEPATH . '../public/uploads/pengajuan', $fileName);

        $data = [
            'nama' => $this->request->getPost('nama'),
            'nik' => $this->request->getPost('nik'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'no_hp' => $this->request->getPost('no_hp'),
            'alamat' => $this->request->getPost('alamat'),
            'unit_kerja' => $this->request->getPost('unit_kerja'),
            'file_pengajuan' => $fileName
        ];

        $this->pengajuanModel->save($data);

        return redirect()->to('/sukses')->with('success', 'Pengajuan akun berhasil dikirim. Tunggu konfirmasi dari admin.');
    }

    // Halaman sukses pengajuan
    public function sukses()
    {
        return view('/sukses_pengajuan');
    }

    // Halaman admin untuk melihat daftar pengajuan
    public function daftarPengajuan()
    {
        if (session()->get('level') != 'admin') {
            return redirect()->to('/login');
        }

        $data = [
            'pengajuan' => $this->pengajuanModel->getAllPengajuan()
        ];

        return view('admin/user/daftar_pengajuan', $data);
    }

    // Proses approve pengajuan
    public function approve($id)
    {
        if (session()->get('level') != 'admin') {
            return redirect()->to('/login');
        }

        $pengajuan = $this->pengajuanModel->find($id);
        if (!$pengajuan) {
            throw new PageNotFoundException("Pengajuan tidak ditemukan");
        }

        // Buat username dari email (ambil bagian sebelum @)
        $username = strstr($pengajuan['email'], '@', true);

        // Cek jika username sudah ada
        $counter = 1;
        $originalUsername = $username;
        while ($this->userModel->where('username', $username)->first()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        // Data untuk user baru
        $userData = [
            'username' => $username,
            'password' => $pengajuan['password'],
            'level' => 'user',
            'nama' => $pengajuan['nama'],
            'nik' => $pengajuan['nik'],
            'email' => $pengajuan['email'],
            'no_hp' => $pengajuan['no_hp'],
            'alamat' => $pengajuan['alamat'],
            'unit_kerja' => $pengajuan['unit_kerja']
        ];

        // Simpan user baru
        $userId = $this->userModel->approveUser($userData);

        // Update pengajuan dengan user_id
        $this->pengajuanModel->update($id, ['user_id' => $userId]);

        // Hapus pengajuan
        $this->pengajuanModel->delete($id);

        return redirect()->to('/admin/pengajuan')->with('success', 'Pengajuan berhasil disetujui dan akun telah dibuat.');
    }

    // Proses hapus pengajuan
    public function delete($id)
    {
        if (session()->get('level') != 'admin') {
            return redirect()->to('/login');
        }

        $pengajuan = $this->pengajuanModel->find($id);
        if (!$pengajuan) {
            throw new PageNotFoundException("Pengajuan tidak ditemukan");
        }

        // Hapus file jika ada
        if ($pengajuan['file_pengajuan']) {
            $filePath = WRITEPATH . '../public/uploads/pengajuan/' . $pengajuan['file_pengajuan'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $this->pengajuanModel->delete($id);

        return redirect()->to('/admin/pengajuan')->with('success', 'Pengajuan berhasil dihapus.');
    }
}
