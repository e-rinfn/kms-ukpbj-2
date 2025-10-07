<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'username',
        'password',
        'level',
        'nama',
        'nik',
        'email',
        'no_hp',
        'alamat',
        'unit_kerja',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Before inserting, hash the password
    // protected $beforeInsert = ['hashPassword'];
    // protected $beforeUpdate = ['hashPassword'];

    // protected function hashPassword(array $data)
    // {
    //     if (isset($data['data']['password'])) {
    //         $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
    //     }
    //     return $data;
    // }

    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
    // Untuk mendapatkan daftar level yang tersedia
    public function getLevelOptions()
    {
        return [
            'admin' => 'Administrator',
            'pegawai' => 'Pegawai',
            'user' => 'User Biasa'
        ];
    }
    public function getUserByNIK($nik)
    {
        return $this->where('nik', $nik)->first();
    }

    public function verifyPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }

    // Rules untuk validasi
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[user.username,id,{id}]',
        'email' => 'required|valid_email|max_length[100]|is_unique[user.email,id,{id}]',
        'nama' => 'required|max_length[100]',
        'nik' => 'permit_empty|numeric|exact_length[16]',
        'no_hp' => 'permit_empty|max_length[15]',
        'level' => 'required|in_list[admin,pegawai,user]',
        'file_pengajuan' => 'permit_empty|max_length[255]'
    ];

    protected $validationMessages = [
        'username' => [
            'is_unique' => 'Username sudah digunakan'
        ],
        'email' => [
            'is_unique' => 'Email sudah digunakan'
        ]
    ];

    public function getUserById($id)
    {
        // Validasi ID
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('ID harus berupa angka');
        }

        // Dapatkan user dengan proteksi terhadap SQL injection
        $user = $this->where('id', (int)$id)
            ->select('id, username, email, level, nama, nik, no_hp, alamat, unit_kerja, created_at, updated_at')
            ->first();

        if (!$user) {
            throw new \RuntimeException('User tidak ditemukan');
        }

        // Jangan kembalikan password hash
        unset($user['password']);

        return $user;
    }

    public function getAllUsers()
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    public function getUserWithSearch($search)
    {
        return $this->groupStart()
            ->like('username', $search)
            ->orLike('nama', $search)
            ->orLike('email', $search)
            ->orLike('nik', $search)
            ->groupEnd()
            ->orderBy('created_at', 'DESC');
    }

    public function createUser($data)
    {
        // Hash password sebelum disimpan
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->insert($data);
    }

    // Method untuk update user
    // public function updateUser($id, array $data)
    // {
    //     // Jika ada password, hash password baru
    //     if (isset($data['password']) && !empty($data['password'])) {
    //         $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    //     } else {
    //         unset($data['password']); // Hapus jika tidak diubah
    //     }

    //     // Update data
    //     $result = $this->update($id, $data);

    //     if (!$result) {
    //         throw new \RuntimeException('Gagal memperbarui data pengguna');
    //     }

    //     return true;
    // }

    public function updateUser($id, $data)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('user');

        try {
            $builder->where('id', $id);
            $result = $builder->update($data);

            if (!$result) {
                $error = $db->error();
                throw new \RuntimeException($error['message']);
            }

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Database error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function approveUser($data)
    {
        // Hash password sebelum disimpan
        $data['password'];
        return $this->insert($data);
    }
}
