<?php

namespace App\Models;

use CodeIgniter\Model;

class KomentarPelatihanModel extends Model
{
    protected $table = 'komentar_pelatihan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pelatihan_id', 'user_id', 'parent_id', 'level', 'komentar', 'created_at'];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    public function getKomentarWithUser($pelatihan_id)
    {
        return $this->select('komentar_pelatihan.*, user.nama as user_nama')
            ->join('user', 'user.id = komentar_pelatihan.user_id')
            ->where(['pelatihan_id' => $pelatihan_id])
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getKomentarByPelatihan($pelatihan_id)
    {
        return $this->select('komentar_pelatihan.*, user.nama as user_nama, user.id as user_id')
            ->join('user', 'user.id = komentar_pelatihan.user_id')
            ->where('pelatihan_id', $pelatihan_id)
            ->where('parent_id IS NULL') // Hanya komentar utama
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getBalasanByParent($parent_id)
    {
        return $this->select('komentar_pelatihan.*, user.nama as user_nama, user.id as user_id')
            ->join('user', 'user.id = komentar_pelatihan.user_id')
            ->where('parent_id', $parent_id)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    public function addKomentar($data)
    {
        return $this->insert($data);
    }

    public function canDelete($comment_id, $user_id)
    {
        $comment = $this->find($comment_id);
        if (!$comment) return false;

        // Admin boleh hapus semua komentar
        if (session()->get('level') === 'admin') return true;

        // User hanya boleh hapus komentar sendiri
        return $comment['user_id'] == $user_id;
    }
}
