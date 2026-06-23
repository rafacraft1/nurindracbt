<?php

namespace App\Models;

use CodeIgniter\Model;

class BankSoalModel extends Model
{
    protected $table            = 'bank_soal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

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

    public function countSoalByJenis(int|string $mapelId, string $jenis): int
    {
        return $this->builder()
            ->where('mapel_id', $mapelId)
            ->where('jenis_soal', $jenis)
            ->countAllResults();
    }

    public function getSoalByMapel(int|string $mapelId): array
    {
        return $this->builder()
            ->select('bank_soal.*, staff.nama_lengkap as nama_guru')
            ->join('staff', 'staff.id = bank_soal.guru_id', 'left')
            ->where('bank_soal.mapel_id', $mapelId)
            ->orderBy('bank_soal.id', 'DESC')
            ->get()
            ->getResultArray();
    }
}
