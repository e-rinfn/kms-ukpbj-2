<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

class VectorStore
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Simpan chunks dan embeddings ke database
     */
    public function saveDocumentChunks($pengetahuanId, $chunks, $embeddings)
    {
        // Hapus embeddings lama jika ada
        $this->db->table('pdf_embeddings')->where('pengetahuan_id', $pengetahuanId)->delete();

        // Simpan chunks baru
        foreach ($chunks as $i => $chunk) {
            if (isset($embeddings[$i]) && !empty($embeddings[$i])) {
                $this->db->table('pdf_embeddings')->insert([
                    'pengetahuan_id' => $pengetahuanId,
                    'chunk_text' => $chunk,
                    'embedding' => json_encode($embeddings[$i]),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    /**
     * Query vector store untuk chunks yang relevan
     */
    public function query($pengetahuanId, $queryEmbedding, $limit = 5)
    {
        if (empty($queryEmbedding)) {
            return [];
        }

        // Untuk database relational sederhana, kita gunakan cosine similarity
        $query = $this->db->table('pdf_embeddings')
            ->select('id, chunk_text, embedding')
            ->where('pengetahuan_id', $pengetahuanId)
            ->get();

        $results = [];
        $queryEmbedding = array_map('floatval', $queryEmbedding);

        foreach ($query->getResult() as $row) {
            $embedding = json_decode($row->embedding, true);
            $similarity = $this->cosineSimilarity($queryEmbedding, $embedding);

            $results[] = [
                'id' => $row->id,
                'text' => $row->chunk_text,
                'similarity' => $similarity
            ];
        }

        // Urutkan berdasarkan similarity tertinggi
        usort($results, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        return array_slice($results, 0, $limit);
    }

    /**
     * Hitung cosine similarity antara dua vector
     */
    protected function cosineSimilarity($vecA, $vecB)
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($vecA as $i => $val) {
            if (isset($vecB[$i])) {
                $dotProduct += $val * $vecB[$i];
                $normA += $val * $val;
                $normB += $vecB[$i] * $vecB[$i];
            }
        }

        if ($normA == 0 || $normB == 0) {
            return 0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
