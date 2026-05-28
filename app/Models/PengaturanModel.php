<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table            = 'pengaturan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'nama_sekolah',
        'kepala_sekolah',
        'nip_kepala_sekolah',
        'alamat_sekolah',
        'email_telepon',
        'logo',
        'tahun_ajaran',
        'semester',
        'zona_waktu',
        'block_multi_login',
        'maintenance_mode'
    ];
}
