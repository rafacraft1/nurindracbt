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

        $rawJadwal = $builder->orderBy('waktu_mulai', 'DESC')->findAll();

        // LOGIKA VIRTUAL GROUPING: Menyatukan jadwal serumpun lintas jurusan ke 1 layar pengawas
        $groupedJadwal = [];
        foreach ($rawJadwal as $j) {
            $key = $j['mapel_id'] . '_' . $j['ruangan_id'] . '_' . strtotime((string)$j['waktu_mulai']) . '_' . $j['pengawas_id'];

            if (!isset($groupedJadwal[$key])) {
                $groupedJadwal[$key] = $j;
                $groupedJadwal[$key]['id_gabungan'] = $j['id'];
                $groupedJadwal[$key]['is_gabungan'] = false;
            } else {
                $groupedJadwal[$key]['id_gabungan'] .= '-' . $j['id'];
                $groupedJadwal[$key]['is_gabungan'] = true;
                $groupedJadwal[$key]['jurusan'] = 'GABUNGAN';
            }
        }

        foreach ($groupedJadwal as &$gj) {
            $gj['id'] = $gj['id_gabungan'];
            if ($gj['is_gabungan']) {
                $gj['nama_ruangan'] .= ' (Campur Kelas)';
            }
        }

        $data = [
            'title'  => 'Daftar Ruang Pengawas - CBT PRO',
            'jadwal' => array_values($groupedJadwal)
        ];

        return view('panel/pengawas_index', $data);
    }

    public function monitor(string $jadwalIdGabungan): ResponseInterface|string
    {
        $ids = explode('-', $jadwalIdGabungan);
        $firstId = $ids[0];

        $jadwal = $this->jadwalModel->select('jadwal_ujian.*, master_mapel.nama_mapel, ruangan.nama_ruangan')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id')
            ->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id')
            ->find($firstId);

        if (!$jadwal) return redirect()->to('/panel/ruang-pengawas')->with('error', 'Jadwal tidak ditemukan.');

        // VALIDASI HAK AKSES PENGAWAS
        if (!$this->verifyPengawasAccess($firstId)) {
            return redirect()->to('/panel/ruang-pengawas')->with('error', 'Akses Ditolak! Anda bukan pengawas di ruangan ini.');
        }

        $jadwals = $this->jadwalModel->whereIn('id', $ids)->findAll();
        $db = \Config\Database::connect();

        // PERBAIKAN: Menggunakan LEFT JOIN agar siswa reguler yang belum mulai ujian tetap tertampil di monitor
        $builder = $db->table('siswa')
            ->select('siswa.id, siswa.nisn, siswa.nama_lengkap, siswa.tingkat, siswa.jurusan, siswa.is_login, hasil_ujian.status as status_ujian, hasil_ujian.is_hadir, hasil_ujian.jadwal_id as actual_jadwal_id')
            ->join('hasil_ujian', "hasil_ujian.siswa_id = siswa.id AND hasil_ujian.jadwal_id IN (" . implode(',', $ids) . ")", 'left');

        $builder->groupStart();
        foreach ($jadwals as $jw) {
            // 1. Filter Reguler: Siswa yang kelas dan ruangannya sesuai dengan pengaturan jadwal
            $builder->orGroupStart()
                ->where('siswa.tingkat', $jw['tingkat'])
                ->where('siswa.jurusan', $jw['jurusan'])
                ->where('siswa.ruangan_id', $jw['ruangan_id'])
                ->groupEnd();
        }
        // 2. Filter Susulan: Menjaring siswa susulan yang beda ruangan tapi sudah dipaksa masuk ke jadwal ini via hasil_ujian
        $builder->orWhere("hasil_ujian.jadwal_id IN (" . implode(',', $ids) . ")");
        $builder->groupEnd();

        $siswa = $builder->orderBy('siswa.nama_lengkap', 'ASC')->get()->getResultArray();

        // Normalisasi null value jika view membutuhkan kepastian nilai
        foreach ($siswa as &$s) {
            $s['is_hadir'] = (int)($s['is_hadir'] ?? 0);
            $s['status_ujian'] = $s['status_ujian'] ?? 'belum_mulai';
        }

        $tokenData = $this->ujianService->getTokenData($firstId);
        $jadwal['id_gabungan'] = $jadwalIdGabungan;

        $data = [
            'title'      => 'Monitoring Ruangan - CBT PRO',
            'jadwal'     => $jadwal,
            'siswa'      => $siswa,
            'token'      => $tokenData['token'],
            'sisa_waktu' => $tokenData['sisa_waktu']
        ];

        return view('panel/pengawas_monitor', $data);
    }

    public function generateTokenAjax(string $jadwalIdGabungan): ResponseInterface
    {
        $ids = explode('-', $jadwalIdGabungan);
        $firstId = $ids[0];

        // VALIDASI IDOR (Endpoint AJAX Protection)
        if (!$this->verifyPengawasAccess($firstId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses Ilegal! Anda tidak berhak mengontrol jadwal ini.']);
        }

        $result = $this->ujianService->generateTokenBaru($firstId);

        if ($result['success']) {
            $tokenFile = FCPATH . 'data_soal/token_' . $firstId . '.json';
            $tokenContent = file_exists($tokenFile) ? file_get_contents($tokenFile) : null;

            // Kloning Token agar seluruh jadwal turunan mengenali token yang sama secara serentak
            foreach ($ids as $id) {
                $jadwal = $this->jadwalModel->find($id);
                if ($jadwal['status'] === 'ready') {
                    $this->jadwalModel->update($id, ['status' => 'active']);
                }
                if ($id != $firstId && $tokenContent) {
                    file_put_contents(FCPATH . 'data_soal/token_' . $id . '.json', $tokenContent);
                }
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
        // VALIDASI IDOR (Aksi Paksa Selesai)
        if (!$this->verifyPengawasAccess($jadwalId)) {
            return redirect()->back()->with('error', 'Akses Ditolak! Anda bukan pengawas untuk sesi ini.');
        }

        $hasil = $this->hasilUjianModel->getHasilByJadwalSiswa($jadwalId, $siswaId);
        if ($hasil) {
            $this->hasilUjianModel->update($hasil['id'], ['status' => 'completed']);
        }
        return redirect()->back()->with('success', 'Ujian siswa berhasil diselesaikan paksa.');
    }

    public function tandaiHadir(string $jadwalId, string $siswaId): ResponseInterface
    {
        // VALIDASI IDOR (Aksi Absensi Fisik)
        if (!$this->verifyPengawasAccess($jadwalId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses Ditolak!']);
        }

        $cek = $this->hasilUjianModel->getHasilByJadwalSiswa($jadwalId, $siswaId);

        if ($cek) {
            $newHadir = (int)$cek['is_hadir'] === 1 ? 0 : 1;
            $this->hasilUjianModel->update($cek['id'], ['is_hadir' => $newHadir]);
            return $this->response->setJSON(['success' => true, 'is_hadir' => $newHadir, 'csrfHash' => csrf_hash()]);
        } else {
            // Pembuatan record baru jika siswa reguler pertama kali ditandai hadir
            $this->hasilUjianModel->insert([
                'jadwal_id' => $jadwalId,
                'siswa_id'  => $siswaId,
                'is_hadir'  => 1,
                'status'    => 'pending'
            ]);
            return $this->response->setJSON(['success' => true, 'is_hadir' => 1, 'csrfHash' => csrf_hash()]);
        }
    }

    /**
     * Private Helper untuk proteksi IDOR Pengawas
     */
    private function verifyPengawasAccess(string $jadwalId): bool
    {
        if (session()->get('role') === 'admin') return true;
        $jadwal = $this->jadwalModel->find($jadwalId);
        return $jadwal && $jadwal['pengawas_id'] == session()->get('id');
    }
}
