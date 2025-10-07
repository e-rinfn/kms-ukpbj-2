<?php

namespace App\Models;

use CodeIgniter\Model;
use Smalot\PdfParser\Parser;

class PengetahuanModel extends Model
{
    protected $table = 'pengetahuan';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'judul',
        'thumbnail_pengetahuan',
        'file_pdf_pengetahuan',
        'caption_pengetahuan',
        'akses_publik',
        'user_id'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Ambil semua data pengetahuan (akses publik) dengan relasi user.
     * Jika $keyword diberikan, akan mencari berdasarkan judul dan caption.
     */
    // Tambahkan method untuk mendapatkan pengetahuan dengan komentar
    public function getWithComments($id)
    {
        return $this->select('pengetahuan.*, user.nama as user_nama')
            ->join('user', 'user.id = pengetahuan.user_id')
            ->where('pengetahuan.id', $id)
            ->first();
    }

    public function getPengetahuanWithUser($keyword = null)
    {
        $builder = $this->db->table($this->table)
            ->select('pengetahuan.*, user.nama as user_nama')
            ->join('user', 'user.id = pengetahuan.user_id', 'left')
            ->where('pengetahuan.akses_publik', 1);

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('pengetahuan.judul', $keyword)
                ->orLike('pengetahuan.caption_pengetahuan', $keyword)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    public function getWithUser($id)
    {
        return $this->select('pengetahuan.*, user.nama as user_nama')
            ->join('user', 'user.id = pengetahuan.user_id', 'left')
            ->where('pengetahuan.id', $id)
            ->first(); // Gunakan first() bukan find() untuk join
    }

    public function getFile($id)
    {
        return $this->select('file_pdf_pengetahuan')
            ->where('id', $id)
            ->first();
    }

    public function getOthers($currentId, $limit = 3)
    {
        return $this->where('id !=', $currentId)
            ->where('file_pdf_pengetahuan IS NOT NULL')
            ->where('file_pdf_pengetahuan !=', '')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getPengetahuanWithUserAdmin($keyword = null)
    {
        $builder = $this->db->table($this->table)
            ->select('pengetahuan.*, user.nama as user_nama')
            ->join('user', 'user.id = pengetahuan.user_id', 'left');
        // Hapus where('pengetahuan.akses_publik') untuk menampilkan SEMUA status akses

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('pengetahuan.judul', $keyword)
                ->orLike('pengetahuan.caption_pengetahuan', $keyword)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }
    /**
     * Ambil satu data pengetahuan berdasarkan ID dengan relasi user.
     * Jika user tidak ditemukan (LEFT JOIN), tetap tampilkan datanya.
     * Berguna untuk halaman edit atau detail admin.
     */
    public function getPengetahuanWithUserById($id)
    {
        $builder = $this->db->table($this->table)
            ->select('pengetahuan.*, IFNULL(user.nama, "Tidak diketahui") as user_nama')
            ->join('user', 'user.id = pengetahuan.user_id', 'left')
            ->where('pengetahuan.id', $id);

        $result = $builder->get()->getRowArray();

        return $result ?: null;
    }

    public function getPengetahuanWithSearch($keyword = null, $perPage = 9)
    {
        $builder = $this->table($this->table);

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('judul', $keyword)
                ->orLike('caption_pengetahuan', $keyword)
                ->groupEnd();
        }

        return $builder->paginate($perPage);
    }

    /**
     * Ambil daftar pengetahuan publik terbaru, bisa dibatasi jumlahnya.
     */
    public function getPublicPengetahuan($limit = null)
    {
        $query = $this->where('akses_publik', 1)
            ->orderBy('created_at', 'DESC');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->findAll();
    }

    /**
     * Ekstrak teks dari file PDF menggunakan Smalot\PdfParser
     */
    public function extractTextFromPdf($filePath)
    {
        try {
            if (!file_exists($filePath)) {
                throw new \Exception("File PDF tidak ditemukan: " . $filePath);
            }

            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();

            if (empty($text)) {
                throw new \Exception("Tidak bisa mengekstrak teks dari PDF");
            }

            return $text;
        } catch (\Exception $e) {
            log_message('error', 'PDF Parse Error: ' . $e->getMessage());
            return "ERROR: " . $e->getMessage();
        }
    }
}
