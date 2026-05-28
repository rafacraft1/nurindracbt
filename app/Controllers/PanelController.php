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

        $data = [
            'title'       => 'Dashboard - CBT PRO',
            'total_siswa' => $siswaModel->countAllResults(),
            'total_guru'  => $staffModel->countByRole('guru'), // Memanggil fungsi dari StaffModel (Fase 8)
            'total_ruang' => $ruanganModel->countAllResults(),
            'ujian_aktif' => $jadwalModel->where('status', 'active')->countAllResults(),
        ];

        return view('panel/dashboard', $data);
    }
}
