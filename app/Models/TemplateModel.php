<?php

namespace App\Models;

use CodeIgniter\Model;
use Smalot\PdfParser\Parser;

class TemplateModel extends Model
{
    protected $table = 'template';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'judul',
        'file_docx',
        'akses_publik',
        'user_id'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Ambil semua data template (akses publik) dengan relasi user.
     * Jika $keyword diberikan, akan mencari berdasarkan judul dan caption.
     */
    // Tambahkan method untuk mendapatkan template dengan komentar
    public function getWithComments($id)
    {
        return $this->select('template.*, user.nama as user_nama')
            ->join('user', 'user.id = template.user_id')
            ->where('template.id', $id)
            ->first();
    }



    public function getTemplateWithUser($keyword = null)
    {
        $builder = $this->db->table($this->table)
            ->select('template.*, user.nama as user_nama')
            ->join('user', 'user.id = template.user_id', 'left')
            ->where('template.akses_publik', 1);

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('template.judul', $keyword)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    public function getWithUser($id)
    {
        return $this->select('template.*, user.nama as user_nama')
            ->join('user', 'user.id = template.user_id', 'left')
            ->where('template.id', $id)
            ->first(); // Gunakan first() bukan find() untuk join
    }

    public function getFile($id)
    {
        return $this->select('file_docx')
            ->where('id', $id)
            ->first();
    }


    public function getOthers($currentId, $limit = 3)
    {
        return $this->where('id !=', $currentId)
            ->where('file_docx IS NOT NULL')
            ->where('file_docx !=', '')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getTemplateWithUserAdmin($keyword = null)
    {
        $builder = $this->db->table($this->table)
            ->select('template.*, user.nama as user_nama')
            ->join('user', 'user.id = template.user_id', 'left');
        // Hapus where('template.akses_publik') untuk menampilkan SEMUA status akses

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('template.judul', $keyword)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }
    /**
     * Ambil satu data template berdasarkan ID dengan relasi user.
     * Jika user tidak ditemukan (LEFT JOIN), tetap tampilkan datanya.
     * Berguna untuk halaman edit atau detail admin.
     */
    public function getTemplateWithUserById($id)
    {
        $builder = $this->db->table($this->table)
            ->select('template.*, IFNULL(user.nama, "Tidak diketahui") as user_nama')
            ->join('user', 'user.id = template.user_id', 'left')
            ->where('template.id', $id);

        $result = $builder->get()->getRowArray();

        return $result ?: null;
    }

    public function getTemplateWithSearch($keyword = null, $perPage = 9)
    {
        $builder = $this->table($this->table);

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('judul', $keyword)
                ->groupEnd();
        }

        return $builder->paginate($perPage);
    }

    /**
     * Ambil daftar template publik terbaru, bisa dibatasi jumlahnya.
     */
    public function getPublicTemplate($limit = null)
    {
        $query = $this->where('akses_publik', 1)
            ->orderBy('created_at', 'DESC');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->findAll();
    }
}
