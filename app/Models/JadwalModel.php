<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalModel extends Model
{
    protected $table            = 'jadwal_ujian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'jenis_ujian_id',
        'mapel_id',
        'tingkat',
        'jurusan',
        'ruangan_id',
        'waktu_mulai',
        'waktu_selesai',
        'durasi',
        'status',
        'pengawas_id',
        'tahun_ajaran',
        'semester',
        'acak_soal',     // Parameter baru
        'tampil_nilai'   // Parameter baru
    ];

    public function getPaginatedJadwal(?string $search, string $sortCol, string $sortDir, int $perPage, int $offset, string $thnAktif, string $smtAktif): array
    {
        $builder = $this->builder()
            ->select('jadwal_ujian.*, master_jenis_ujian.nama_ujian, master_mapel.nama_mapel, ruangan.nama_ruangan, staff.nama_lengkap as nama_pengawas')
            ->join('master_jenis_ujian', 'master_jenis_ujian.id = jadwal_ujian.jenis_ujian_id', 'left')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id', 'left')
            ->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id', 'left')
            ->join('staff', 'staff.id = jadwal_ujian.pengawas_id', 'left')
            ->where('jadwal_ujian.tahun_ajaran', $thnAktif)
            ->where('jadwal_ujian.semester', $smtAktif);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('master_mapel.nama_mapel', $search)
                ->orLike('jadwal_ujian.tingkat', $search)
                ->orLike('jadwal_ujian.jurusan', $search)
                ->groupEnd();
        }

        return $builder->orderBy($sortCol, $sortDir)->limit($perPage, $offset)->get()->getResultArray();
    }

    public function countTotalJadwal(?string $search, string $thnAktif, string $smtAktif): int
    {
        $builder = $this->builder()
            ->where('jadwal_ujian.tahun_ajaran', $thnAktif)
            ->where('jadwal_ujian.semester', $smtAktif);

        if (!empty($search)) {
            $builder->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id', 'left');
            $builder->groupStart()
                ->like('master_mapel.nama_mapel', $search)
                ->orLike('jadwal_ujian.tingkat', $search)
                ->orLike('jadwal_ujian.jurusan', $search)
                ->groupEnd();
        }
        return $builder->countAllResults();
    }
}
