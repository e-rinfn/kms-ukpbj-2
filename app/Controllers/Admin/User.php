<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class User extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Cek level admin
        if (session()->get('level') != 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }

        $search = $this->request->getGet('search');
        $filterLevel = $this->request->getGet('level'); // ambil filter level dari form

        $userModel = new \App\Models\UserModel();
        $query = $userModel;

        if ($search) {
            $users = $this->userModel->getUserWithSearch($search)->paginate(10);
        } else {
            $users = $this->userModel->paginate(10);
        }

        // Jika ada pencarian
        if (!empty($search)) {
            $query = $query->groupStart()
                ->like('username', $search)
                ->orLike('nama', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        // Jika ada filter level
        if (!empty($filterLevel)) {
            $query = $query->where('level', $filterLevel);
        }

        $data = [
            'title' => 'Kelola Pengguna',
            'users' => $users,
            'filterLevel' => $filterLevel,
            'pager' => $this->userModel->pager,
            'search' => $search
        ];

        return view('admin/user/index', $data);
    }

    public function create()
    {
        // Cek level admin
        if (session()->get('level') != 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }

        $data = [
            'title' => 'Tambah Pengguna',
            'validation' => \Config\Services::validation()
        ];

        return view('admin/user/create', $data);
    }

    public function store()
    {
        // Cek level admin
        if (session()->get('level') != 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }

        // Validasi
        if (!$this->validate($this->userModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'level' => $this->request->getPost('level'),
            'nama' => $this->request->getPost('nama'),
            'nik' => $this->request->getPost('nik'),
            'email' => $this->request->getPost('email'),
            'no_hp' => $this->request->getPost('no_hp'),
            'alamat' => $this->request->getPost('alamat'),
            'unit_kerja' => $this->request->getPost('unit_kerja')
        ];

        if ($this->userModel->createUser($data)) {
            return redirect()->to('/admin/user')->with('success', 'Pengguna berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan pengguna');
        }
    }

    public function edit($id)
    {
        if (session()->get('level') != 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }

        try {
            $user = $this->userModel->getUserById($id);

            $data = [
                'title' => 'Ubah Pengguna',
                'user' => $user,
                'validation' => \Config\Services::validation(),
                'errors' => session()->getFlashdata('errors') ?? [],
                'error' => session()->getFlashdata('error') ?? null
            ];

            return view('admin/user/edit', $data);
        } catch (\Exception $e) {
            return redirect()->to('/admin/user')->with('error', $e->getMessage());
        }
    }

    public function update($id)
    {
        // Cek akses admin
        if (session()->get('level') != 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }

        // Validasi input
        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[user.username,id,{$id}]",
            'email' => "required|valid_email|max_length[100]|is_unique[user.email,id,{$id}]",
            'nama' => 'required|max_length[100]',
            'nik' => 'permit_empty|numeric|exact_length[16]',
            'no_hp' => 'permit_empty|max_length[15]',
            'level' => 'required|in_list[admin,pegawai,user]',
            'unit_kerja' => 'permit_empty|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'nama' => $this->request->getPost('nama'),
                'nik' => $this->request->getPost('nik'),
                'no_hp' => $this->request->getPost('no_hp'),
                'alamat' => $this->request->getPost('alamat'),
                'unit_kerja' => $this->request->getPost('unit_kerja'),
                'level' => $this->request->getPost('level'),
                'updated_at' => date('Y-m-d H:i:s') // Pastikan di-update
            ];

            // Jika password diisi, hash password baru
            if ($this->request->getPost('password')) {
                $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            }

            // Update data
            $this->userModel->updateUser($id, $data);

            return redirect()->to('/admin/user')->with('success', 'Data pengguna berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        // Cek level admin
        if (session()->get('level') != 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }

        // Cegah penghapusan diri sendiri
        if ($id == session()->get('id')) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        if ($this->userModel->delete($id)) {
            return redirect()->to('/admin/user')->with('success', 'Pengguna berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus pengguna');
        }
    }
}
