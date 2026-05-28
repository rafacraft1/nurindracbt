<?php

namespace App\Models;

use CodeIgniter\Model;

class BankSoalModel extends Model
{
    protected $table            = 'bank_soal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Strict typing array fields
    protected $allowedFields    = [
        'mapel_id',
        'guru_id',
        'jenis_soal',
        'pertanyaan',
        'opsi_jawaban',
        'kunci_jawaban',
        'file_audio',
        'created_at'
    ];

    /**
     * Hitung jumlah soal berdasarkan jenis (PG/Essai)
     */
    public function countSoalByJenis(string $mapelId, string $jenis): int
    {
        return $this->where('mapel_id', $mapelId)
            ->where('jenis_soal', $jenis)
            ->countAllResults();
    }

    /**
     * Ambil data soal beserta nama guru pembuatnya
     */
    public function getSoalByMapel(string $mapelId): array
    {
        return $this->select('bank_soal.*, staff.nama_lengkap as nama_guru')
            ->join('staff', 'staff.id = bank_soal.guru_id', 'left')
            ->where('bank_soal.mapel_id', $mapelId)
            ->orderBy('bank_soal.id', 'DESC')
            ->findAll();
    }
}
