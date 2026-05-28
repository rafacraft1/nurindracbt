<?php

namespace App\Controllers;

use App\Models\JadwalModel;
use App\Models\HasilUjianModel;
use App\Models\SiswaModel;
use App\Services\UjianService;
use CodeIgniter\HTTP\ResponseInterface;

class PengawasController extends BaseController
{
    protected JadwalModel $jadwalModel;
    protected HasilUjianModel $hasilUjianModel;
    protected SiswaModel $siswaModel;
    protected UjianService $ujianService;

    public function __construct()
    {
        $this->jadwalModel     = new JadwalModel();
        $this->hasilUjianModel = new HasilUjianModel();
        $this->siswaModel      = new SiswaModel();
        $this->ujianService    = new UjianService();
    }

    public function index(): string
    {
        $role       = (string)session()->get('role');
        $pengawasId = (string)session()->get('id');

        $builder = $this->jadwalModel->select('jadwal_ujian.*, master_mapel.nama_mapel, ruangan.nama_ruangan')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id', 'left')
            ->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id', 'left')
            ->where('jadwal_ujian.tahun_ajaran', $this->tahunAktif)
            ->where('jadwal_ujian.semester', $this->smtAktif);

        if ($role !== 'admin') {
            $builder->where('pengawas_id', $pengawasId);
        }

        $data = [
            'title'  => 'Daftar Ruang Pengawas - CBT PRO',
            'jadwal' => $builder->orderBy('waktu_mulai', 'DESC')->findAll()
        ];

        return view('panel/pengawas_index', $data);
    }

    public function monitor(string $jadwalId): ResponseInterface|string
    {
        $jadwal = $this->jadwalModel->select('jadwal_ujian.*, master_mapel.nama_mapel, ruangan.nama_ruangan')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id')
            ->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id')
            ->find($jadwalId);

        if (!$jadwal) return redirect()->to('/panel/ruang-pengawas')->with('error', 'Jadwal tidak ditemukan.');

        if (session()->get('role') !== 'admin' && $jadwal['pengawas_id'] != session()->get('id')) {
            return redirect()->to('/panel/ruang-pengawas')->with('error', 'Akses Ditolak! Anda bukan pengawas di ruangan ini.');
        }

        $db = \Config\Database::connect();
        $siswa = $db->table('siswa')
            ->select('siswa.id, siswa.nisn, siswa.nama_lengkap, siswa.is_login, hasil_ujian.status as status_ujian, hasil_ujian.is_hadir')
            ->join('hasil_ujian', "hasil_ujian.siswa_id = siswa.id AND hasil_ujian.jadwal_id = $jadwalId", 'left')
            ->where('siswa.ruangan_id', $jadwal['ruangan_id'])
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()->getResultArray();

        $tokenData = $this->ujianService->getTokenData($jadwalId);

        $data = [
            'title'      => 'Monitoring Ruangan - CBT PRO',
            'jadwal'     => $jadwal,
            'siswa'      => $siswa,
            'token'      => $tokenData['token'],
            'sisa_waktu' => $tokenData['sisa_waktu']
        ];

        return view('panel/pengawas_monitor', $data);
    }

    public function generateTokenAjax(string $jadwalId): ResponseInterface
    {
        $result = $this->ujianService->generateTokenBaru($jadwalId);

        if ($result['success']) {
            $jadwal = $this->jadwalModel->find($jadwalId);
            if ($jadwal['status'] === 'ready') {
                $this->jadwalModel->update($jadwalId, ['status' => 'active']);
            }

            return $this->response->setJSON([
                'success'  => true,
                'token'    => $result['token'],
                'csrfHash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menulis file token!']);
    }

    public function resetLogin(string $siswaId): ResponseInterface
    {
        $this->siswaModel->update($siswaId, ['is_login' => 0]);
        return redirect()->back()->with('success', 'Sesi login siswa berhasil direset.');
    }

    public function forceSelesai(string $jadwalId, string $siswaId): ResponseInterface
    {
        $hasil = $this->hasilUjianModel->getHasilByJadwalSiswa($jadwalId, $siswaId);
        if ($hasil) {
            $this->hasilUjianModel->update($hasil['id'], ['status' => 'completed']);
        }
        return redirect()->back()->with('success', 'Ujian siswa berhasil diselesaikan paksa.');
    }

    public function tandaiHadir(string $jadwalId, string $siswaId): ResponseInterface
    {
        $cek = $this->hasilUjianModel->getHasilByJadwalSiswa($jadwalId, $siswaId);

        if ($cek) {
            $newHadir = (int)$cek['is_hadir'] === 1 ? 0 : 1;
            $this->hasilUjianModel->update($cek['id'], ['is_hadir' => $newHadir]);
            return $this->response->setJSON(['success' => true, 'is_hadir' => $newHadir, 'csrfHash' => csrf_hash()]);
        } else {
            $this->hasilUjianModel->insert([
                'jadwal_id' => $jadwalId,
                'siswa_id'  => $siswaId,
                'is_hadir'  => 1,
                'status'    => 'pending'
            ]);
            return $this->response->setJSON(['success' => true, 'is_hadir' => 1, 'csrfHash' => csrf_hash()]);
        }
    }
}
