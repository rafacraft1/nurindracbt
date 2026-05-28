<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table            = 'siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'nisn',
        'password',
        'password_plain',
        'nama_lengkap',
        'tingkat',
        'jurusan',
        'rombel',
        'ruangan_id',
        'is_login',
        'last_active',
        'created_at'
    ];

    /**
     * Ambil data siswa dengan pagination, pencarian, dan pengurutan dinamis
     */
    public function getPaginatedSiswa(?string $search, string $sortCol, string $sortDir, int $perPage, int $offset): array
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->groupStart()
                ->like('nama_lengkap', $search)
                ->orLike('nisn', $search)
                ->groupEnd();
        }

        if ($sortCol === 'nisn') {
            $builder->orderBy('nisn', $sortDir);
        } elseif ($sortCol === 'nama') {
            $builder->orderBy('nama_lengkap', $sortDir);
        } else {
            $builder->orderBy('tingkat', $sortDir)
                ->orderBy('jurusan', $sortDir)
                ->orderBy('rombel', $sortDir)
                ->orderBy('nama_lengkap', 'ASC');
        }

        return $builder->limit($perPage, $offset)->get()->getResultArray();
    }

    /**
     * Hitung total siswa untuk kalkulasi halaman (Pagination)
     */
    public function countTotalSiswa(?string $search = null): int
    {
        $builder = $this->builder();
        if (!empty($search)) {
            $builder->groupStart()
                ->like('nama_lengkap', $search)
                ->orLike('nisn', $search)
                ->groupEnd();
        }
        return $builder->countAllResults();
    }

    /**
     * Cek apakah NISN sudah terdaftar
     */
    public function isNisnExist(string $nisn, ?string $excludeId = null): bool
    {
        $builder = $this->where('nisn', $nisn);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }

    /**
     * Fungsi helper untuk mengosongkan ruangan secara massal
     */
    public function kosongkanRuangan(string $ruanganId): bool
    {
        return $this->where('ruangan_id', $ruanganId)->set(['ruangan_id' => null])->update();
    }
}
