<?php

namespace App\Models;

use CodeIgniter\Model;

class KomentarPengetahuanModel extends Model
{
    protected $table = 'komentar_pengetahuan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pengetahuan_id', 'user_id', 'komentar'];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    public function getKomentarWithUser($pengetahuan_id)
    {
        return $this->select('komentar_pengetahuan.*, user.nama as user_nama')
            ->join('user', 'user.id = komentar_pengetahuan.user_id')
            ->where(['pengetahuan_id' => $pengetahuan_id])
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getKomentarByPengetahuan($pengetahuan_id)
    {
        return $this->select('komentar_pengetahuan.*, user.nama as user_nama')
            ->join('user', 'user.id = komentar_pengetahuan.user_id')
            ->where('pengetahuan_id', $pengetahuan_id)
            ->orderBy('created_at', 'DESC')
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
