<?php

namespace App\Models;

use CodeIgniter\Model;

class HasilUjianModel extends Model
{
    protected $table            = 'hasil_ujian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'jadwal_id',
        'siswa_id',
        'is_hadir',
        'status',
        'waktu_mulai_ujian',
        'waktu_selesai_ujian',
        'jawaban_peserta',
        'nilai_pg',
        'nilai_essai'
    ];

    public function getHasilByJadwalSiswa(int|string $jadwalId, int|string $siswaId): ?array
    {
        return $this->where('jadwal_id', $jadwalId)
            ->where('siswa_id', $siswaId)
            ->first();
    }
}
