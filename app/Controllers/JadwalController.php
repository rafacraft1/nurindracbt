<?php

namespace App\Controllers;

use App\Models\JadwalModel;
use App\Models\JenisUjianModel;
use App\Models\MapelModel;
use App\Models\RuanganModel;
use App\Services\UjianService;
use CodeIgniter\HTTP\ResponseInterface;

class JadwalController extends BaseController
{
    protected JadwalModel $jadwalModel;
    protected UjianService $ujianService;

    public function __construct()
    {
        $this->jadwalModel  = new JadwalModel();
        $this->ujianService = new UjianService();
    }

    public function index(): string
    {
        $search  = $this->request->getGet('search');
        $page    = max((int)($this->request->getGet('page') ?? 1), 1);
        $sortCol = (string)($this->request->getGet('sort') ?? 'waktu');
        $sortDir = strtoupper((string)($this->request->getGet('dir') ?? 'DESC'));
        $sortDir = in_array($sortDir, ['ASC', 'DESC']) ? $sortDir : 'DESC';

        $allowedSorts = [
            'waktu'   => 'jadwal_ujian.waktu_mulai',
            'mapel'   => 'master_mapel.nama_mapel',
            'ruangan' => 'ruangan.nama_ruangan',
            'status'  => 'jadwal_ujian.status'
        ];
        $dbSortCol = $allowedSorts[$sortCol] ?? 'jadwal_ujian.waktu_mulai';

        $perPage = 25;
        $offset  = ($page - 1) * $perPage;

        $totalData  = $this->jadwalModel->countTotalJadwal($search, $this->tahunAktif, $this->smtAktif);
        $totalPages = (int)ceil($totalData / $perPage);
        $jadwal     = $this->jadwalModel->getPaginatedJadwal($search, $dbSortCol, $sortDir, $perPage, $offset, $this->tahunAktif, $this->smtAktif);

        $currentTime = time();
        foreach ($jadwal as &$j) {
            if ($j['status'] === 'active' && $currentTime > strtotime((string)$j['waktu_selesai'])) {
                $this->jadwalModel->update($j['id'], ['status' => 'finished']);
                $j['status'] = 'finished';
            }
        }

        $db = \Config\Database::connect();
        $mapelModel = new MapelModel();
        $mapel = $mapelModel->findAll();

        foreach ($mapel as &$m) {
            $m['total_pg']    = $db->table('bank_soal')->where('mapel_id', $m['id'])->where('jenis_soal', 'pg')->countAllResults();
            $m['total_essai'] = $db->table('bank_soal')->where('mapel_id', $m['id'])->where('jenis_soal', 'essai')->countAllResults();
        }

        $jenisUjianModel = new JenisUjianModel();
        $ruanganModel    = new RuanganModel();

        // Mengambil data unik dari tabel siswa untuk kebutuhan sugesti datalist dropdown
        $listTingkat = $db->table('siswa')->select('tingkat')->where('tingkat !=', '')->distinct()->orderBy('tingkat', 'ASC')->get()->getResultArray();
        $listJurusan = $db->table('siswa')->select('jurusan')->where('jurusan !=', '')->where('jurusan IS NOT NULL')->distinct()->orderBy('jurusan', 'ASC')->get()->getResultArray();

        $data = [
            'title'       => 'Manajemen Jadwal - CBT PRO',
            'jadwal'      => $jadwal,
            'jenis_ujian' => $jenisUjianModel->findAll(),
            'mapel'       => $mapel,
            'ruangan'     => $ruanganModel->findAll(),
            'semua_guru'  => $db->table('staff')->where('role', 'guru')->orderBy('nama_lengkap', 'ASC')->get()->getResultArray(),
            'listTingkat' => $listTingkat,
            'listJurusan' => $listJurusan,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'totalData'   => $totalData,
            'search'      => $search,
            'sortCol'     => $sortCol,
            'sortDir'     => $sortDir
        ];

        return view('panel/jadwal', $data);
    }

    public function store(): ResponseInterface
    {
        $waktuMulai   = $this->request->getPost('waktu_mulai');
        $waktuSelesai = $this->request->getPost('waktu_selesai');
        $ruanganId    = $this->request->getPost('ruangan_id');
        $tingkat      = strtoupper((string)$this->request->getPost('tingkat'));
        $jurusan      = strtoupper((string)$this->request->getPost('jurusan'));

        if (strtotime((string)$waktuSelesai) <= strtotime((string)$waktuMulai)) {
            return redirect()->back()->with('error', 'Gagal! Waktu selesai tidak boleh lebih awal atau sama dengan waktu mulai.');
        }

        // VALIDASI KEAMANAN: Mencegah bentrok pemakaian ruangan atau bentrok jadwal dalam satu kelas
        $bentrok = $this->jadwalModel
            ->groupStart()
            ->where('ruangan_id', $ruanganId)
            ->orGroupStart()
            ->where('tingkat', $tingkat)
            ->where('jurusan', $jurusan)
            ->groupEnd()
            ->groupEnd()
            ->where('waktu_mulai <', $waktuSelesai)
            ->where('waktu_selesai >', $waktuMulai)
            ->where('tahun_ajaran', $this->tahunAktif)
            ->where('semester', $this->smtAktif)
            ->first();

        if ($bentrok) {
            $pesanBentrok = ($bentrok['ruangan_id'] == $ruanganId)
                ? 'Ruangan sudah terpakai oleh jadwal ujian lain pada rentang jam tersebut!'
                : "Kelas $tingkat $jurusan sudah memiliki agenda ujian lain pada jam tersebut!";
            return redirect()->back()->with('error', 'BENTROK: ' . $pesanBentrok);
        }

        $this->jadwalModel->insert([
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'mapel_id'       => $this->request->getPost('mapel_id'),
            'tingkat'        => $tingkat,
            'jurusan'        => $jurusan,
            'ruangan_id'     => $ruanganId,
            'waktu_mulai'    => $waktuMulai,
            'waktu_selesai'  => $waktuSelesai,
            'durasi'         => $this->request->getPost('durasi'),
            'status'         => 'draft',
            'pengawas_id'    => null,
            'tahun_ajaran'   => $this->tahunAktif,
            'semester'       => $this->smtAktif
        ]);

        return redirect()->back()->with('success', 'Kerangka jadwal berhasil dibuat. Silakan Plot Pengawas.');
    }

    public function update(string $id): ResponseInterface
    {
        $jadwal = $this->jadwalModel->find($id);

        $waktuMulai   = $this->request->getPost('waktu_mulai');
        $waktuSelesai = $this->request->getPost('waktu_selesai');
        $ruanganId    = $this->request->getPost('ruangan_id');
        $tingkat      = strtoupper((string)$this->request->getPost('tingkat'));
        $jurusan      = strtoupper((string)$this->request->getPost('jurusan'));

        if (strtotime((string)$waktuSelesai) <= strtotime((string)$waktuMulai)) {
            return redirect()->back()->with('error', 'Gagal! Waktu selesai tidak boleh lebih awal atau sama dengan waktu mulai.');
        }

        $dataUpdate = [
            'waktu_mulai'    => $waktuMulai,
            'waktu_selesai'  => $waktuSelesai,
            'durasi'         => $this->request->getPost('durasi'),
        ];

        if ($jadwal['status'] === 'active') {
            // Ketika status aktif berjalan, hanya durasi/waktu yang boleh diperpanjang
            $dataUpdate['jenis_ujian_id'] = $jadwal['jenis_ujian_id'];
            $dataUpdate['mapel_id']       = $jadwal['mapel_id'];
            $dataUpdate['tingkat']        = $jadwal['tingkat'];
            $dataUpdate['jurusan']        = $jadwal['jurusan'];
            $dataUpdate['ruangan_id']     = $jadwal['ruangan_id'];
        } else {
            $bentrok = $this->jadwalModel
                ->where('id !=', $id)
                ->groupStart()
                ->where('ruangan_id', $ruanganId)
                ->orGroupStart()
                ->where('tingkat', $tingkat)
                ->where('jurusan', $jurusan)
                ->groupEnd()
                ->groupEnd()
                ->where('waktu_mulai <', $waktuSelesai)
                ->where('waktu_selesai >', $waktuMulai)
                ->where('tahun_ajaran', $this->tahunAktif)
                ->where('semester', $this->smtAktif)
                ->first();

            if ($bentrok) {
                return redirect()->back()->with('error', 'Update Gagal! Terjadi bentrok Ruangan atau Kelas dengan jadwal lain.');
            }

            $dataUpdate['jenis_ujian_id'] = $this->request->getPost('jenis_ujian_id');
            $dataUpdate['mapel_id']       = $this->request->getPost('mapel_id');
            $dataUpdate['tingkat']        = $tingkat;
            $dataUpdate['jurusan']        = $jurusan;
            $dataUpdate['ruangan_id']     = $ruanganId;

            if ($jadwal['mapel_id'] != $dataUpdate['mapel_id']) {
                $dataUpdate['status'] = 'draft';
            } else if ($jadwal['status'] === 'finished' && strtotime((string)$dataUpdate['waktu_selesai']) > time()) {
                $dataUpdate['status'] = 'ready';
            }
        }

        $this->jadwalModel->update($id, $dataUpdate);
        $pesan = $jadwal['status'] === 'active'
            ? 'Waktu ujian berhasil diperpanjang! (Data komponen utama di kunci karena sedang berlangsung)'
            : 'Jadwal ujian berhasil diperbarui!';

        return redirect()->back()->with('success', $pesan);
    }

    public function delete(string $id): ResponseInterface
    {
        $jadwal = $this->jadwalModel->find($id);
        if ($jadwal['status'] === 'active') {
            return redirect()->back()->with('error', 'Akses Ditolak! Ujian sedang berjalan, jadwal tidak boleh dihapus.');
        }

        // Penghapusan Berkas JSON Soal
        $jsonPath = FCPATH . 'data_soal/jadwal_' . $id . '.json';
        if (file_exists($jsonPath)) unlink($jsonPath);

        // GARBAGE COLLECTOR: Menghapus file token pengawas agar tidak menumpuk memenuhi storage server
        $tokenPath = FCPATH . 'data_soal/token_' . $id . '.json';
        if (file_exists($tokenPath)) unlink($tokenPath);

        $this->jadwalModel->delete($id);
        return redirect()->back()->with('success', 'Jadwal, berkas engine JSON, dan token pengawas berhasil dibersihkan dari server.');
    }

    public function plotPengawas(): ResponseInterface
    {
        $jadwalId   = (string)$this->request->getPost('jadwal_id');
        $pengawasId = (string)$this->request->getPost('pengawas_id');

        if (empty($pengawasId)) {
            $this->jadwalModel->update($jadwalId, ['pengawas_id' => null]);
            return redirect()->back()->with('success', 'Pengawas berhasil dilepas dari jadwal.');
        }

        $jadwalTarget = $this->jadwalModel->find($jadwalId);
        $startTarget  = strtotime((string)$jadwalTarget['waktu_mulai']);
        $endTarget    = $startTarget + ($jadwalTarget['durasi'] * 60);

        $jadwalLain = $this->jadwalModel->where('pengawas_id', $pengawasId)->where('id !=', $jadwalId)->findAll();

        foreach ($jadwalLain as $jl) {
            $startLain = strtotime((string)$jl['waktu_mulai']);
            $endLain   = $startLain + ($jl['durasi'] * 60);

            if ($startTarget < $endLain && $endTarget > $startLain) {
                return redirect()->back()->with('error', 'BENTROK! Guru tersebut sudah terjadwal mengawas di ruangan lain pada jam kerja ini.');
            }
        }

        $this->jadwalModel->update($jadwalId, ['pengawas_id' => $pengawasId]);
        return redirect()->back()->with('success', 'Pengawas berhasil di-plot tanpa bentrok!');
    }

    public function generateJson(string $jadwalId): ResponseInterface
    {
        $jadwal = $this->jadwalModel->find($jadwalId);
        if (!$jadwal) return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');

        if ($this->ujianService->generateJsonSoal($jadwalId, $jadwal['mapel_id'])) {
            return redirect()->back()->with('success', 'Engine Ready! File JSON statis berhasil di-generate.');
        }

        return redirect()->back()->with('error', 'Gagal Generate! Bank soal kosong atau folder public/data_soal tidak memiliki izin tulis.');
    }
}
