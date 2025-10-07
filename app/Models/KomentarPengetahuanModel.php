<?php

namespace App\Models;

use CodeIgniter\Model;

class KomentarPengetahuanModel extends Model
{
    protected $table = 'komentar_pengetahuan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pengetahuan_id', 'user_id', 'parent_id', 'level', 'komentar', 'created_at'];
    protected $useTimestamps = false;

    public function getKomentarByPengetahuan($pengetahuan_id)
    {
        return $this->select('komentar_pengetahuan.*, user.nama as user_nama, user.id as user_id')
            ->join('user', 'user.id = komentar_pengetahuan.user_id')
            ->where('pengetahuan_id', $pengetahuan_id)
            ->where('parent_id IS NULL') // Hanya komentar utama
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getBalasanByParent($parent_id)
    {
        return $this->select('komentar_pengetahuan.*, user.nama as user_nama, user.id as user_id')
            ->join('user', 'user.id = komentar_pengetahuan.user_id')
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

        if (session()->get('level') === 'admin') return true;

        return $comment['user_id'] == $user_id;
    }
}
