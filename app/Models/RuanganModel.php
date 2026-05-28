<?php

namespace App\Models;

use CodeIgniter\Model;

class RuanganModel extends Model
{
    protected $table            = 'ruangan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_ruangan'];

    /**
     * Ambil data ruangan beserta total siswa di dalamnya
     */
    public function getRuanganDenganJumlahSiswa(): array
    {
        return $this->select('ruangan.*, COUNT(siswa.id) as jumlah_siswa')
            ->join('siswa', 'siswa.ruangan_id = ruangan.id', 'left')
            ->groupBy('ruangan.id')
            ->orderBy('ruangan.nama_ruangan', 'ASC')
            ->findAll();
    }
}
