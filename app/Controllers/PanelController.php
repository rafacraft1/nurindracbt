<?php

namespace App\Controllers;

use App\Models\SiswaModel;
use App\Models\StaffModel;
use App\Models\RuanganModel;
use App\Models\JadwalModel;

class PanelController extends BaseController
{
    public function dashboard(): string
    {
        $siswaModel   = new SiswaModel();
        $staffModel   = new StaffModel();
        $ruanganModel = new RuanganModel();
        $jadwalModel  = new JadwalModel();

        $db = \Config\Database::connect();
        $pengaturan = $db->table('pengaturan')->where('id', 1)->get()->getRowArray();

        $jadwalAktif = $jadwalModel->select('jadwal_ujian.*, master_mapel.nama_mapel')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id', 'left')
            ->where('jadwal_ujian.status', 'active')
            ->orderBy('jadwal_ujian.id', 'DESC')
            ->findAll(5);

        $jadwalTerdekat = $jadwalModel->select('jadwal_ujian.*, master_mapel.nama_mapel')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id', 'left')
            ->where('jadwal_ujian.status !=', 'active')
            ->orderBy('jadwal_ujian.waktu_mulai', 'DESC')
            ->findAll(4);

        $data = [
            'title'           => 'Dashboard - CBT PRO',
            'pengaturan'      => $pengaturan,
            'total_siswa'     => $siswaModel->countAllResults(),
            'total_guru'      => $staffModel->countByRole('guru'),
            'total_ruang'     => $ruanganModel->countAllResults(),
            'ujian_aktif'     => $jadwalModel->where('status', 'active')->countAllResults(),
            'jadwal_aktif'    => $jadwalAktif,
            'jadwal_terdekat' => $jadwalTerdekat,
            'role'            => session()->get('role'),
            'is_panitia'      => session()->get('is_panitia'),
            'total_bank_soal' => 0,
        ];

        if ($data['role'] === 'guru' && $db->tableExists('bank_soal')) {
            $data['total_bank_soal'] = $db->table('bank_soal')
                ->where('guru_id', session()->get('id'))
                ->countAllResults();
        }

        return view('panel/dashboard', $data);
    }
}
