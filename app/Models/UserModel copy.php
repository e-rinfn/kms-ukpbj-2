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
        'unit_kerja'
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

    public function getUserByNIK($nik)
    {
        return $this->where('nik', $nik)->first();
    }

    public function verifyPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }

    public function getAllUsersQuery($search = null)
    {
        $builder = $this->builder();

        if ($search) {
            $builder->groupStart()
                ->like('username', $search)
                ->orLike('nama', $search)
                ->orLike('email', $search)
                ->orLike('nik', $search)
                ->groupEnd();
        }

        return $builder;
    }

    public function searchUsers($keyword)
    {
        return $this->like('nama', $keyword)
            ->orLike('username', $keyword)
            ->orLike('email', $keyword)
            ->orLike('nik', $keyword)
            ->findAll();
    }

    public function updateUserProfile($id, $data)
    {
        // Remove password if empty
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $this->update($id, $data);
    }

    // public function createUser($data)
    // {
    //     // Hash password sebelum disimpan
    //     $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    //     return $this->insert($data);
    // }

    public function createUser($data)
    {
        // Hash password sebelum disimpan
        $data['password'];
        return $this->insert($data);
    }
}
