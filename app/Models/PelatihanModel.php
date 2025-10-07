<?php

namespace App\Models;

use CodeIgniter\Model;

class PelatihanModel extends Model
{
    protected $table = 'pelatihan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'judul',
        'video_pelatihan',
        'link_youtube',
        'caption_pelatihan',
        'akses_publik',
        'user_id'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Ambil semua data pelatihan (akses publik) dengan relasi user.
     * Jika $keyword diberikan, akan mencari berdasarkan judul dan caption.
     */
    public function getPelatihanWithUser($keyword = null)
    {
        $builder = $this->db->table($this->table)
            ->select('pelatihan.*, user.nama as user_nama')
            ->join('user', 'user.id = pelatihan.user_id', 'left')
            ->where('pelatihan.akses_publik', 1);

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('pelatihan.judul', $keyword)
                ->orLike('pelatihan.caption_pelatihan', $keyword)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }
    public function getPelatihanWithUserAdmin($keyword = null)
    {
        $builder = $this->db->table($this->table)
            ->select('pelatihan.*, user.nama as user_nama')
            ->join('user', 'user.id = pelatihan.user_id', 'left');
        // Hapus where('pelatihan.akses_publik') untuk menampilkan SEMUA status akses

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('pelatihan.judul', $keyword)
                ->orLike('pelatihan.caption_pelatihan', $keyword)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Ambil satu data pelatihan berdasarkan ID dengan relasi user.
     * Jika user tidak ditemukan (LEFT JOIN), tetap tampilkan datanya.
     * Berguna untuk halaman edit atau detail admin.
     */
    public function getPelatihanWithUserById($id)
    {
        $builder = $this->db->table($this->table)
            ->select('pelatihan.*, IFNULL(user.nama, "Tidak diketahui") as user_nama')
            ->join('user', 'user.id = pelatihan.user_id', 'left')
            ->where('pelatihan.id', $id);

        $result = $builder->get()->getRowArray();

        return $result ?: null;
    }

    public function getPelatihanWithSearch($keyword = null, $perPage = 9)
    {
        $builder = $this->table($this->table);

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('judul', $keyword)
                ->orLike('caption_pelatihan', $keyword)
                ->groupEnd();
        }

        return $builder->paginate($perPage);
    }

    /**
     * Ambil daftar pengetahuan publik terbaru, bisa dibatasi jumlahnya.
     */
    public function getPublicPelatihan($limit = null)
    {
        $builder = $this->where('akses_publik', 1)
            ->orderBy('created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }
}
