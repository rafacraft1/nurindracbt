<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

class UjianService
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function generateJsonSoal(string $jadwalId, string $mapelId): bool
    {
        $soal = $this->db->table('bank_soal')->where('mapel_id', $mapelId)->get()->getResultArray();

        if (empty($soal)) {
            return false;
        }

        $soalClean = [];
        foreach ($soal as $s) {
            unset($s['kunci_jawaban']);
            $soalClean[] = $s;
        }

        $jsonContent = json_encode($soalClean);
        $filePath    = FCPATH . 'data_soal/jadwal_' . $jadwalId . '.json';

        if (file_put_contents($filePath, $jsonContent)) {
            $this->db->table('jadwal_ujian')->where('id', $jadwalId)->update(['status' => 'ready']);
            return true;
        }

        return false;
    }

    /**
     * Memvalidasi kebenaran Token Ujian
     */
    public function validateToken(string $jadwalId, string $tokenInput): bool
    {
        $jsonPath = FCPATH . 'data_ruangan/token_' . $jadwalId . '.json';
        if (!file_exists($jsonPath)) return false;

        $tokenData = json_decode((string)file_get_contents($jsonPath), true);
        $serverToken = $tokenData['token'] ?? '';

        // LOGIKA BYPASS: Jika pengawas membebaskan token
        if ($serverToken === 'FREE') return true;

        return strtoupper($tokenInput) === $serverToken;
    }

    /**
     * Generate Token secara acak oleh Pengawas
     */
    public function generateTokenBaru(string $jadwalId): array
    {
        $token = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        $jsonContent = json_encode([
            'token'      => $token,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $filePath = FCPATH . 'data_ruangan/token_' . $jadwalId . '.json';
        $success  = file_put_contents($filePath, $jsonContent) !== false;

        return ['success' => $success, 'token' => $token];
    }

    /**
     * Fitur Bebaskan Token (Tanpa Token)
     */
    public function bebaskanToken(string $jadwalId): array
    {
        $jsonContent = json_encode([
            'token'      => 'FREE',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $filePath = FCPATH . 'data_ruangan/token_' . $jadwalId . '.json';
        $success  = file_put_contents($filePath, $jsonContent) !== false;

        return ['success' => $success, 'token' => 'FREE'];
    }

    /**
     * Mengambil informasi Token untuk ditampilkan di panel Pengawas
     */
    public function getTokenData(string $jadwalId): array
    {
        $jsonPath = FCPATH . 'data_ruangan/token_' . $jadwalId . '.json';
        if (file_exists($jsonPath)) {
            $tokenData = json_decode((string)file_get_contents($jsonPath), true);
            if (isset($tokenData['updated_at'])) {

                // Deteksi status Bebas Token
                if (($tokenData['token'] ?? '') === 'FREE') {
                    return [
                        'token'      => 'BEBAS TOKEN',
                        'sisa_waktu' => 9999
                    ];
                }

                $elapsed = time() - strtotime((string)$tokenData['updated_at']);
                $sisaWaktuDetik = max(0, 900 - $elapsed);

                return [
                    'token'      => $tokenData['token'] ?? 'BELUM ADA',
                    'sisa_waktu' => $sisaWaktuDetik
                ];
            }
        }
        return ['token' => 'BELUM ADA', 'sisa_waktu' => 900];
    }
}
