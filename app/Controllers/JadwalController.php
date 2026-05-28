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

        $data = [
            'title'       => 'Manajemen Jadwal - CBT PRO',
            'jadwal'      => $jadwal,
            'jenis_ujian' => $jenisUjianModel->findAll(),
            'mapel'       => $mapel,
            'ruangan'     => $ruanganModel->findAll(),
            'semua_guru'  => $db->table('staff')->where('role', 'guru')->orderBy('nama_lengkap', 'ASC')->get()->getResultArray(),
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
        $this->jadwalModel->insert([
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'mapel_id'       => $this->request->getPost('mapel_id'),
            'tingkat'        => strtoupper((string)$this->request->getPost('tingkat')),
            'jurusan'        => strtoupper((string)$this->request->getPost('jurusan')),
            'ruangan_id'     => $this->request->getPost('ruangan_id'),
            'waktu_mulai'    => $this->request->getPost('waktu_mulai'),
            'waktu_selesai'  => $this->request->getPost('waktu_selesai'),
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

        $dataUpdate = [
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'mapel_id'       => $this->request->getPost('mapel_id'),
            'tingkat'        => strtoupper((string)$this->request->getPost('tingkat')),
            'jurusan'        => strtoupper((string)$this->request->getPost('jurusan')),
            'ruangan_id'     => $this->request->getPost('ruangan_id'),
            'waktu_mulai'    => $this->request->getPost('waktu_mulai'),
            'waktu_selesai'  => $this->request->getPost('waktu_selesai'),
            'durasi'         => $this->request->getPost('durasi'),
        ];

        if ($jadwal['status'] === 'active') {
            unset($dataUpdate['jenis_ujian_id'], $dataUpdate['mapel_id'], $dataUpdate['tingkat'], $dataUpdate['jurusan'], $dataUpdate['ruangan_id']);
        } else {
            if ($jadwal['mapel_id'] != $dataUpdate['mapel_id']) {
                $dataUpdate['status'] = 'draft';
            } else if ($jadwal['status'] === 'finished' && strtotime((string)$dataUpdate['waktu_selesai']) > time()) {
                $dataUpdate['status'] = 'ready';
            }
        }

        $this->jadwalModel->update($id, $dataUpdate);
        $pesan = $jadwal['status'] === 'active'
            ? 'Waktu ujian berhasil diperpanjang! (Data mapel/ruangan diabaikan karena sedang berlangsung)'
            : 'Jadwal ujian berhasil diperbarui!';

        return redirect()->back()->with('success', $pesan);
    }

    public function delete(string $id): ResponseInterface
    {
        $jadwal = $this->jadwalModel->find($id);
        if ($jadwal['status'] === 'active') {
            return redirect()->back()->with('error', 'Akses Ditolak! Ujian sedang berlangsung, jadwal tidak boleh dihapus.');
        }

        $jsonPath = FCPATH . 'data_soal/jadwal_' . $id . '.json';
        if (file_exists($jsonPath)) unlink($jsonPath);

        $this->jadwalModel->delete($id);
        return redirect()->back()->with('success', 'Jadwal dan file soal statis berhasil dihapus.');
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
                return redirect()->back()->with('error', 'BENTROK! Guru yang dipilih sudah mengawas di ruangan/jadwal lain pada jam tersebut.');
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
