<?php

namespace App\Models;

use CodeIgniter\Model;

class PengajuanModel extends Model
{
    protected $table = 'pengajuan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama',
        'nik',
        'email',
        'password',
        'no_hp',
        'alamat',
        'unit_kerja',
        'file_pengajuan',
        'user_id'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Before inserting, hash the password
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function getPengajuanWithUser($id = false)
    {
        if ($id === false) {
            return $this->select('pengajuan.*, user.username as user_username, user.nama as user_nama')
                ->join('user', 'user.id = pengajuan.user_id', 'left')
                ->findAll();
        }

        return $this->select('pengajuan.*, user.username as user_username, user.nama as user_nama')
            ->join('user', 'user.id = pengajuan.user_id', 'left')
            ->where(['pengajuan.id' => $id])
            ->first();
    }
    public function getAllPengajuan()
    {
        // Ambil semua data diurutkan dari yang terbaru
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    public function getPengajuanByUserId($user_id)
    {
        return $this->where('user_id', $user_id)->first();
    }

    // public function approvePengajuan($id)
    // {
    //     $pengajuan = $this->find($id);
    //     if (!$pengajuan) {
    //         return false;
    //     }

    //     // Create user account from pengajuan data
    //     $userData = [
    //         'nama' => $pengajuan['nama'],
    //         'nik' => $pengajuan['nik'],
    //         'email' => $pengajuan['email'],
    //         'password' => password_hash($pengajuan['password'], PASSWORD_DEFAULT),
    //         'no_hp' => $pengajuan['no_hp'],
    //         'alamat' => $pengajuan['alamat'],
    //         'unit_kerja' => $pengajuan['unit_kerja'],
    //         'level' => 'user'
    //     ];

    //     $userModel = new UserModel();
    //     $user_id = $userModel->insert($userData);

    //     // Update pengajuan with user_id
    //     $this->update($id, ['id' => $user_id]);

    //     return $user_id;
    // }

    public function approve($id)
    {
        if (session()->get('level') != 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }

        // Validasi ID
        if (!is_numeric($id)) {
            return redirect()->back()->with('error', 'ID tidak valid');
        }

        // Cari pengajuan
        $pengajuan = $this->pengajuanModel->find($id);
        if (!$pengajuan) {
            return redirect()->back()->with('error', 'Pengajuan tidak ditemukan');
        }

        // Generate username unik dari email
        $username = strstr($pengajuan['email'], '@', true);
        $originalUsername = $username;
        $counter = 1;

        // Cek username unik
        while ($this->userModel->where('username', $username)->first()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        // Data untuk user baru
        $userData = [
            'username' => $username,
            'password' => $pengajuan['password'],
            // 'password' => password_hash($pengajuan['password'], PASSWORD_DEFAULT),
            'level' => 'user',
            'nama' => $pengajuan['nama'],
            'nik' => $pengajuan['nik'],
            'email' => $pengajuan['email'],
            'no_hp' => $pengajuan['no_hp'],
            'alamat' => $pengajuan['alamat'],
            'unit_kerja' => $pengajuan['unit_kerja'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Mulai transaction
        $this->db->transBegin();

        try {
            // Simpan user baru
            if (!$this->userModel->insert($userData)) {
                throw new \RuntimeException('Gagal menyimpan user baru');
            }

            // Hapus pengajuan
            if (!$this->pengajuanModel->delete($id)) {
                throw new \RuntimeException('Gagal menghapus pengajuan');
            }

            // Commit transaction
            $this->db->transCommit();

            return redirect()->to('/admin/pengajuan')->with('success', 'Pengajuan berhasil disetujui. Akun baru telah dibuat.');
        } catch (\Exception $e) {
            // Rollback transaction jika ada error
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function rejectPengajuan($id)
    {
        return $this->delete($id);
    }
}
