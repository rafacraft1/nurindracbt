<?php

namespace App\Models;

use CodeIgniter\Model;

class MapelModel extends Model
{
    protected $table            = 'master_mapel';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_mapel'];

    /**
     * Mengatasi N+1 Query Problem. 
     * Mengambil Mapel, Total Soal PG, Total Essai, dan Guru Pengampu DALAM 2 QUERY SAJA!
     */
    public function getMapelWithStats(): array
    {
        $db = \Config\Database::connect();

        // Query 1: Ambil Mapel dan hitung agregasi tipe soal
        $mapel = $this->builder()
            ->select('master_mapel.id, master_mapel.nama_mapel')
            ->select('COALESCE(SUM(CASE WHEN bank_soal.jenis_soal = "pg" THEN 1 ELSE 0 END), 0) as total_pg')
            ->select('COALESCE(SUM(CASE WHEN bank_soal.jenis_soal = "essai" THEN 1 ELSE 0 END), 0) as total_essai')
            ->join('bank_soal', 'bank_soal.mapel_id = master_mapel.id', 'left')
            ->groupBy('master_mapel.id')
            ->orderBy('master_mapel.nama_mapel', 'ASC')
            ->get()->getResultArray();

        // Query 2: Ambil SEMUA relasi Guru sekaligus
        $guruMapel = $db->table('guru_mapel')
            ->select('guru_mapel.mapel_id, staff.id, staff.nama_lengkap')
            ->join('staff', 'staff.id = guru_mapel.guru_id')
            ->get()->getResultArray();

        // Kelompokkan guru berdasarkan mapel_id ke dalam Memory Array (Super Cepat)
        $guruByMapel = [];
        foreach ($guruMapel as $gm) {
            $guruByMapel[$gm['mapel_id']][] = [
                'id'           => $gm['id'],
                'nama_lengkap' => $gm['nama_lengkap']
            ];
        }

        // Satukan data guru ke array mapel
        foreach ($mapel as &$m) {
            $m['guru_pengampu'] = $guruByMapel[$m['id']] ?? [];
        }

        return $mapel;
    }
}
