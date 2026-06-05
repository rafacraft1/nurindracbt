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

        if (!$this->verifyPengawasAccess($firstId)) {
            return redirect()->to('/panel/ruang-pengawas')->with('error', 'Akses Ditolak! Anda bukan pengawas di ruangan ini.');
        }

        $jadwals = $this->jadwalModel->whereIn('id', $ids)->findAll();
        $db = \Config\Database::connect();

        $builder = $db->table('siswa')
            ->select('siswa.id, siswa.nisn, siswa.nama_lengkap, siswa.tingkat, siswa.jurusan, siswa.is_login, hasil_ujian.status as status_ujian, hasil_ujian.is_hadir, hasil_ujian.jadwal_id as actual_jadwal_id')
            ->join('hasil_ujian', "hasil_ujian.siswa_id = siswa.id AND hasil_ujian.jadwal_id IN (" . implode(',', $ids) . ")", 'left');

        $builder->groupStart();
        foreach ($jadwals as $jw) {
            $builder->orGroupStart()
                ->where('siswa.tingkat', $jw['tingkat'])
                ->where('siswa.jurusan', $jw['jurusan'])
                ->where('siswa.ruangan_id', $jw['ruangan_id'])
                ->groupEnd();
        }
        $builder->orWhere("hasil_ujian.jadwal_id IN (" . implode(',', $ids) . ")");
        $builder->groupEnd();

        $siswa = $builder->orderBy('siswa.nama_lengkap', 'ASC')->get()->getResultArray();

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

        if (!$this->verifyPengawasAccess($firstId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses Ilegal! Anda tidak berhak mengontrol jadwal ini.']);
        }

        $jadwalUtama = $this->jadwalModel->find($firstId);
        $sekarang    = time();
        $mulai       = strtotime((string)$jadwalUtama['waktu_mulai']);
        $selesai     = strtotime((string)$jadwalUtama['waktu_selesai']);

        if ($sekarang < $mulai) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'BELUM WAKTUNYA! Token hanya dapat dirilis pada ' . date('d M Y, H:i', $mulai)
            ]);
        }

        if ($sekarang > $selesai) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'WAKTU HABIS! Jadwal ujian ini sudah ditutup secara sistem.'
            ]);
        }

        $result = $this->ujianService->generateTokenBaru($firstId);

        if ($result['success']) {
            // FIX BUG PATH: Sebelumnya data_soal/, seharusnya data_ruangan/
            $tokenFile = FCPATH . 'data_ruangan/token_' . $firstId . '.json';
            $tokenContent = file_exists($tokenFile) ? file_get_contents($tokenFile) : null;

            foreach ($ids as $id) {
                $jadwal = $this->jadwalModel->find($id);
                if ($jadwal['status'] === 'ready' && $sekarang >= $mulai && $sekarang <= $selesai) {
                    $this->jadwalModel->update($id, ['status' => 'active']);
                }
                if ($id != $firstId && $tokenContent) {
                    file_put_contents(FCPATH . 'data_ruangan/token_' . $id . '.json', $tokenContent);
                }
            }

            return $this->response->setJSON([
                'success'  => true,
                'token'    => $result['token'],
                'csrfHash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menulis file token di server!']);
    }

    // FUNGSI BARU: AJAX Bebaskan Token
    public function bebaskanTokenAjax(string $jadwalIdGabungan): ResponseInterface
    {
        $ids = explode('-', $jadwalIdGabungan);
        $firstId = $ids[0];

        if (!$this->verifyPengawasAccess($firstId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses Ilegal!']);
        }

        $jadwalUtama = $this->jadwalModel->find($firstId);
        $sekarang    = time();
        $mulai       = strtotime((string)$jadwalUtama['waktu_mulai']);
        $selesai     = strtotime((string)$jadwalUtama['waktu_selesai']);

        if ($sekarang < $mulai) {
            return $this->response->setJSON(['success' => false, 'message' => 'BELUM WAKTUNYA!']);
        }
        if ($sekarang > $selesai) {
            return $this->response->setJSON(['success' => false, 'message' => 'WAKTU HABIS!']);
        }

        $result = $this->ujianService->bebaskanToken($firstId);

        if ($result['success']) {
            $tokenFile = FCPATH . 'data_ruangan/token_' . $firstId . '.json';
            $tokenContent = file_exists($tokenFile) ? file_get_contents($tokenFile) : null;

            foreach ($ids as $id) {
                $jadwal = $this->jadwalModel->find($id);
                if ($jadwal['status'] === 'ready' && $sekarang >= $mulai && $sekarang <= $selesai) {
                    $this->jadwalModel->update($id, ['status' => 'active']);
                }
                if ($id != $firstId && $tokenContent) {
                    file_put_contents(FCPATH . 'data_ruangan/token_' . $id . '.json', $tokenContent);
                }
            }

            return $this->response->setJSON([
                'success'  => true,
                'token'    => 'BEBAS TOKEN',
                'csrfHash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal membypass token di server!']);
    }

    public function resetLogin(string $siswaId): ResponseInterface
    {
        $jadwalId = (string)$this->request->getPost('jadwal_id');

        if (empty($jadwalId) || !$this->verifyPengawasAccess($jadwalId)) {
            return redirect()->back()->with('error', 'Akses Ditolak! Anda bukan pengawas untuk sesi siswa ini.');
        }

        $this->siswaModel->update($siswaId, ['is_login' => 0]);
        return redirect()->back()->with('success', 'Sesi login siswa berhasil direset.');
    }

    public function forceSelesai(string $jadwalId, string $siswaId): ResponseInterface
    {
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
        if (!$this->verifyPengawasAccess($jadwalId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses Ditolak!']);
        }

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

    private function verifyPengawasAccess(string $jadwalId): bool
    {
        if (session()->get('role') === 'admin') return true;

        $ids = explode('-', $jadwalId);
        $jadwalUtama = $this->jadwalModel->find($ids[0]);

        return $jadwalUtama && $jadwalUtama['pengawas_id'] == session()->get('id');
    }
}
