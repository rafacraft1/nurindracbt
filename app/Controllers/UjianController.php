<?php

namespace App\Controllers;

use App\Models\JadwalModel;
use App\Models\HasilUjianModel;
use App\Models\BankSoalModel;
use App\Models\SiswaModel;
use App\Services\UjianService;
use CodeIgniter\HTTP\ResponseInterface;

class UjianController extends BaseController
{
    protected JadwalModel $jadwalModel;
    protected HasilUjianModel $hasilUjianModel;
    protected BankSoalModel $bankSoalModel;
    protected SiswaModel $siswaModel;
    protected UjianService $ujianService;

    public function __construct()
    {
        $this->jadwalModel     = new JadwalModel();
        $this->hasilUjianModel = new HasilUjianModel();
        $this->bankSoalModel   = new BankSoalModel();
        $this->siswaModel      = new SiswaModel();
        $this->ujianService    = new UjianService();
    }

    public function index(): string
    {
        $siswa = session()->get();
        $now   = date('Y-m-d H:i:s');

        $this->jadwalModel->where('waktu_selesai <=', $now)
            ->whereIn('status', ['ready', 'active'])
            ->set(['status' => 'finished'])
            ->update();

        $db = \Config\Database::connect();
        $riwayat = $db->table('hasil_ujian')
            ->select('hasil_ujian.*, jadwal_ujian.mapel_id')
            ->join('jadwal_ujian', 'jadwal_ujian.id = hasil_ujian.jadwal_id', 'left')
            ->where('hasil_ujian.siswa_id', $siswa['id'])
            ->get()->getResultArray();

        $statusUjian       = [];
        $kehadiran         = [];
        $jadwalProgress    = [];
        $mapelSelesai      = [];
        $jadwalPreInserted = [];

        foreach ($riwayat as $r) {
            $statusUjian[$r['jadwal_id']] = $r['status'];
            $kehadiran[$r['jadwal_id']]   = $r['is_hadir'];
            $jadwalPreInserted[]          = $r['jadwal_id'];

            if ($r['status'] === 'progress') {
                $jadwalProgress[] = $r['jadwal_id'];
            }
            if ($r['status'] === 'completed') {
                $mapelSelesai[] = $r['mapel_id'];
            }
        }

        $builder = $this->jadwalModel->select('jadwal_ujian.*, master_mapel.nama_mapel, master_jenis_ujian.nama_ujian')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id')
            ->join('master_jenis_ujian', 'master_jenis_ujian.id = jadwal_ujian.jenis_ujian_id')
            ->where('jadwal_ujian.tingkat', $siswa['tingkat'])
            ->where('jadwal_ujian.tahun_ajaran', $this->tahunAktif)
            ->where('jadwal_ujian.semester', $this->smtAktif);

        if (!empty($jadwalPreInserted)) {
            $builder->groupStart()
                ->groupStart()
                ->where('jadwal_ujian.ruangan_id', $siswa['ruangan_id'])
                ->where('jadwal_ujian.jurusan', $siswa['jurusan'])
                ->groupEnd()
                ->orWhereIn('jadwal_ujian.id', $jadwalPreInserted)
                ->groupEnd();
        } else {
            $builder->where('jadwal_ujian.ruangan_id', $siswa['ruangan_id'])
                ->where('jadwal_ujian.jurusan', $siswa['jurusan']);
        }

        if (!empty($jadwalProgress)) {
            $builder->groupStart()
                ->whereIn('jadwal_ujian.status', ['ready', 'active'])
                ->orWhereIn('jadwal_ujian.id', $jadwalProgress)
                ->groupEnd();
        } else {
            $builder->whereIn('jadwal_ujian.status', ['ready', 'active']);
        }

        $rawJadwal = $builder->orderBy('jadwal_ujian.waktu_mulai', 'ASC')->findAll();

        $jadwalAktif = [];
        foreach ($rawJadwal as $j) {
            $isPreInserted = in_array($j['id'], $jadwalPreInserted);

            if ($isPreInserted) {
                $jadwalAktif[] = $j;
                continue;
            }
            if (in_array($j['mapel_id'], $mapelSelesai)) {
                continue;
            }
            if (stripos($j['nama_ujian'], 'susulan') !== false) {
                continue;
            }
            $jadwalAktif[] = $j;
        }

        $data = [
            'title'       => 'Lobi Ujian - CBT PRO',
            'jadwalAktif' => $jadwalAktif,
            'statusUjian' => $statusUjian,
            'kehadiran'   => $kehadiran
        ];

        return view('ujian/index', $data);
    }

    public function mulai(): ResponseInterface
    {
        $jadwalId   = (string)$this->request->getPost('jadwal_id');
        $tokenInput = strtoupper((string)$this->request->getPost('token'));
        $siswaId    = (string)session()->get('id');

        $jadwal = $this->jadwalModel->find($jadwalId);

        if ($jadwal['waktu_selesai'] <= date('Y-m-d H:i:s')) {
            return redirect()->back()->with('error', 'Akses Ditolak! Jadwal ujian ini sudah ditutup.');
        }

        if (!$this->ujianService->validateToken($jadwalId, $tokenInput)) {
            return redirect()->back()->with('error', 'Token salah atau belum dirilis Pengawas!');
        }

        $cekHasil = $this->hasilUjianModel->getHasilByJadwalSiswa($jadwalId, $siswaId);

        if (!$cekHasil || (int)$cekHasil['is_hadir'] === 0) {
            return redirect()->back()->with('error', 'Akses Ditolak! Anda belum diabsen oleh Pengawas.');
        }

        if ($cekHasil['status'] === 'completed') {
            return redirect()->back()->with('error', 'Anda sudah menyelesaikan ujian ini!');
        }

        if ($cekHasil['status'] === 'pending') {
            $this->hasilUjianModel->update($cekHasil['id'], [
                'status'            => 'progress',
                'waktu_mulai_ujian' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to('/ujian/kerjakan/' . $jadwalId);
    }

    public function kerjakan(string $jadwalId): ResponseInterface|string
    {
        $siswaId = (string)session()->get('id');
        $hasil   = $this->hasilUjianModel->getHasilByJadwalSiswa($jadwalId, $siswaId);

        if (!$hasil || $hasil['status'] !== 'progress') {
            return redirect()->to('/ujian')->with('error', 'Akses ilegal. Silakan masukkan token terlebih dahulu.');
        }

        $jadwal = $this->jadwalModel->select('jadwal_ujian.*, master_mapel.nama_mapel')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id')
            ->find($jadwalId);

        $absoluteDeadline = strtotime((string)$jadwal['waktu_selesai']) + (15 * 60);

        if (time() > $absoluteDeadline) {
            $this->hasilUjianModel->update($hasil['id'], ['status' => 'completed']);
            $this->siswaModel->update($siswaId, ['is_login' => 0]);
            return redirect()->to('/ujian')->with('error', 'Waktu toleransi habis. Jawaban disubmit otomatis.');
        }

        $data = [
            'title'  => 'Mengerjakan: ' . $jadwal['nama_mapel'],
            'jadwal' => $jadwal,
            'hasil'  => $hasil
        ];

        return view('ujian/kerjakan', $data);
    }

    public function simpanJawabanAjax(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses ilegal']);
        }

        $jadwalId     = (string)$this->request->getPost('jadwal_id');
        $jawabanSiswa = (string)$this->request->getPost('jawaban');
        $siswaId      = (string)session()->get('id');

        json_decode($jawabanSiswa);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'   => 'error',
                'message'  => 'Gagal menyimpan! Payload jawaban corrupt.',
                'csrfHash' => csrf_hash()
            ]);
        }

        $hasil = $this->hasilUjianModel->getHasilByJadwalSiswa($jadwalId, $siswaId);

        if ($hasil && $hasil['status'] === 'progress') {
            $this->hasilUjianModel->update($hasil['id'], [
                'jawaban_peserta' => $jawabanSiswa
            ]);

            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => 'Autosave berhasil',
                'csrfHash' => csrf_hash()
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Sesi ujian tidak valid atau sudah selesai'
        ]);
    }

    public function submit(): ResponseInterface
    {
        $payloadJson = (string)$this->request->getPost('payload_jawaban');
        if (empty($payloadJson)) return redirect()->to('/ujian')->with('error', 'Gagal mengirim jawaban. Data kosong.');

        $payload = json_decode($payloadJson, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($payload['jadwal_id'], $payload['siswa_id'], $payload['jawaban'])) {
            return redirect()->to('/ujian')->with('error', 'Format data pengiriman tidak valid.');
        }

        $jadwalId     = (string)$payload['jadwal_id'];
        $siswaId      = (string)$payload['siswa_id'];
        $jawabanSiswa = $payload['jawaban'];

        $hasil = $this->hasilUjianModel->getHasilByJadwalSiswa($jadwalId, $siswaId);

        if (!$hasil || $hasil['status'] === 'completed') {
            $this->siswaModel->update($siswaId, ['is_login' => 0]);
            return redirect()->to('/ujian')->with('success', 'Ujian Anda telah berhasil direkam sebelumnya.');
        }

        $jadwal   = $this->jadwalModel->find($jadwalId);
        $bankSoal = $this->bankSoalModel->where('mapel_id', $jadwal['mapel_id'])->findAll();

        $kunciAsli = [];
        foreach ($bankSoal as $s) {
            $kunciAsli[$s['id']] = $s;
        }

        $benar      = 0;
        $totalGanda = 0;

        foreach ($jawabanSiswa as $idSoal => $data) {
            $soalDB = $kunciAsli[$idSoal] ?? null;
            if (!$soalDB) continue;

            if ($soalDB['jenis_soal'] === 'pg') {
                $totalGanda++;
                if (!empty($data['jawab']) && strtolower((string)$data['jawab']) === strtolower((string)$soalDB['kunci_jawaban'])) {
                    $benar++;
                }
            }
        }

        $nilai_pg = $totalGanda > 0 ? ($benar / $totalGanda) * 100 : 0;

        $db = \Config\Database::connect();
        $db->transStart();

        $this->hasilUjianModel->update($hasil['id'], [
            'jawaban_peserta'     => json_encode($jawabanSiswa),
            'nilai_pg'            => round($nilai_pg, 2),
            'status'              => 'completed',
            'waktu_selesai_ujian' => date('Y-m-d H:i:s')
        ]);

        $this->siswaModel->update($siswaId, ['is_login' => 0]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/ujian')->with('error', 'Terjadi kesalahan sistem saat menyimpan nilai. Segera lapor pengawas!');
        }

        return redirect()->to('/ujian')->with('success', 'Selamat! Ujian diselesaikan dan Nilai berhasil direkam.');
    }
}
