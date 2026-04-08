<?php

/**
 * ============================================================================
 * CBT PRO - ENTERPRISE EDITION
 * ============================================================================
 *
 * @package    Nurindra CBT PRO
 * @author     Nurindra
 * @copyright  2026 Nurindra CBT PRO
 * @version    1.0.0
 *
 * @description CBT PRO adalah platform Ujian Berbasis Komputer (Computer Based
 * Test) berskala Enterprise yang dirancang untuk performa tinggi, keamanan
 * absolut, dan manajemen akademik terintegrasi untuk institusi modern.
 * Aplikasi ini boleh digunakan dan di sebarluaskan secara gratis
 *
 * ----------------------------------------------------------------------------
 * HUBUNGI PENGEMBANG:
 * Contact Person : Nurindra
 * Email          : nurindra.id@gmail.com
 * WhatsApp       : +62 812-2032-9780
 * YouTube        : https://www.youtube.com/@nurindraid
 * Instagram      : https://www.instagram.com/kevinecraft
 * TikTok         : https://www.tiktok.com/@kevinecraft1
 * ----------------------------------------------------------------------------
 * PERINGATAN HAK CIPTA:
 * Kode sumber ini dilindungi oleh kekayaan intelektual. Dilarang keras
 * memodifikasi atau menjual ulang bagian manapun dari aplikasi ini 
 * tanpa izin tertulis dari pihak pengembang.
 * ============================================================================
 */


namespace App\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PanitiaController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function ruangan()
    {
        $ruangan = $this->db->table('ruangan')
            ->select('ruangan.*, COUNT(siswa.id) as jumlah_siswa')
            ->join('siswa', 'siswa.ruangan_id = ruangan.id', 'left')
            ->groupBy('ruangan.id')
            ->orderBy('ruangan.nama_ruangan', 'ASC')
            ->get()->getResultArray();

        $siswa = $this->db->table('siswa')
            ->select('id, nisn, nama_lengkap, tingkat, jurusan, rombel, ruangan_id')
            ->orderBy('tingkat', 'ASC')
            ->orderBy('jurusan', 'ASC')
            ->orderBy('rombel', 'ASC')
            ->orderBy('nama_lengkap', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title'   => 'Manajemen Ruangan - CBT PRO',
            'ruangan' => $ruangan,
            'siswa'   => $siswa
        ];

        return view('panel/ruangan', $data);
    }

    public function storeRuangan()
    {
        $namaRuangan = $this->request->getPost('nama_ruangan');

        if ($this->db->table('ruangan')->where('nama_ruangan', $namaRuangan)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Nama Ruangan sudah ada!');
        }

        $this->db->table('ruangan')->insert(['nama_ruangan' => strtoupper($namaRuangan)]);
        return redirect()->back()->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function deleteRuangan($id)
    {
        $this->db->table('siswa')->where('ruangan_id', $id)->update(['ruangan_id' => null]);
        $this->db->table('ruangan')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Ruangan berhasil dihapus.');
    }

    public function plotSiswaRuangan()
    {
        $ruangan_id = $this->request->getPost('ruangan_id');
        $siswa_ids  = $this->request->getPost('siswa_ids') ?? [];

        $this->db->table('siswa')->where('ruangan_id', $ruangan_id)->update(['ruangan_id' => null]);

        if (!empty($siswa_ids)) {
            $this->db->table('siswa')->whereIn('id', $siswa_ids)->update(['ruangan_id' => $ruangan_id]);
        }

        return redirect()->back()->with('success', 'Data penghuni ruangan berhasil disinkronisasi.');
    }

    public function kosongkanRuangan($id)
    {
        $this->db->table('siswa')->where('ruangan_id', $id)->update(['ruangan_id' => null]);
        return redirect()->back()->with('success', 'Ruangan berhasil dikosongkan.');
    }

    public function siswa()
    {
        $search  = $this->request->getGet('search');
        $page    = (int)($this->request->getGet('page') ?? 1);
        if ($page < 1) $page = 1;

        $perPage = 50;
        $offset  = ($page - 1) * $perPage;
        $builderCount = $this->db->table('siswa');
        if (!empty($search)) {
            $builderCount->groupStart()
                ->like('nama_lengkap', $search)
                ->orLike('nisn', $search)
                ->groupEnd();
        }
        $totalData  = $builderCount->countAllResults();
        $totalPages = ceil($totalData / $perPage);
        $builderData = $this->db->table('siswa');
        if (!empty($search)) {
            $builderData->groupStart()
                ->like('nama_lengkap', $search)
                ->orLike('nisn', $search)
                ->groupEnd();
        }

        $siswa = $builderData->orderBy('tingkat', 'ASC')
            ->orderBy('jurusan', 'ASC')
            ->orderBy('rombel', 'ASC')
            ->orderBy('nama_lengkap', 'ASC')
            ->limit($perPage, $offset)
            ->get()->getResultArray();

        $ruangan = $this->db->table('ruangan')->get()->getResultArray();

        $data = [
            'title'       => 'Data Siswa - CBT PRO',
            'siswa'       => $siswa,
            'ruangan'     => $ruangan,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'totalData'   => $totalData,
            'search'      => $search
        ];

        return view('panel/siswa', $data);
    }

    public function storeSiswa()
    {
        $nisn = $this->request->getPost('nisn');

        if ($this->db->table('siswa')->where('nisn', $nisn)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'NISN sudah terdaftar di sistem!');
        }

        $passwordPlain = $this->request->getPost('password') ?: 'siswa123';

        $dataInsert = [
            'nisn'         => $nisn,
            'password'     => password_hash($passwordPlain, PASSWORD_DEFAULT),
            'nama_lengkap' => strtoupper($this->request->getPost('nama_lengkap')),
            'tingkat'      => strtoupper($this->request->getPost('tingkat')),
            'jurusan'      => strtoupper($this->request->getPost('jurusan')),
            'rombel'       => strtoupper($this->request->getPost('rombel')),
            'ruangan_id'   => $this->request->getPost('ruangan_id') ?: null,
            'created_at'   => date('Y-m-d H:i:s')
        ];

        $this->db->table('siswa')->insert($dataInsert);
        return redirect()->back()->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function updateSiswa($id)
    {
        $dataUpdate = [
            'nama_lengkap' => strtoupper($this->request->getPost('nama_lengkap')),
            'tingkat'      => strtoupper($this->request->getPost('tingkat')),
            'jurusan'      => strtoupper($this->request->getPost('jurusan')),
            'rombel'       => strtoupper($this->request->getPost('rombel')),
            'ruangan_id'   => $this->request->getPost('ruangan_id') ?: null,
        ];

        $passwordBaru = $this->request->getPost('password');
        if (!empty($passwordBaru)) {
            $dataUpdate['password'] = password_hash($passwordBaru, PASSWORD_DEFAULT);
        }

        $this->db->table('siswa')->where('id', $id)->update($dataUpdate);
        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function deleteSiswa($id)
    {
        $this->db->table('siswa')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Data siswa berhasil dihapus.');
    }

    public function importSiswa()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'Pilih file Excel yang valid!');
        }

        $extension = $file->getClientExtension();
        if (!in_array($extension, ['xls', 'xlsx'])) {
            return redirect()->back()->with('error', 'Format file harus .xls atau .xlsx');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray();

            $dataInsert = [];
            $jumlahSukses = 0;
            $jumlahGagal  = 0;

            foreach ($rows as $key => $row) {
                if ($key == 0) continue;

                $nisn = trim($row[0] ?? '');
                $nama = trim($row[1] ?? '');

                if (empty($nisn) || empty($nama)) continue;

                if ($this->db->table('siswa')->where('nisn', $nisn)->countAllResults() > 0) {
                    $jumlahGagal++;
                    continue;
                }

                $passwordPlain = trim($row[5] ?? '') ?: 'siswa123';

                $dataInsert[] = [
                    'nisn'         => $nisn,
                    'password'     => password_hash($passwordPlain, PASSWORD_DEFAULT),
                    'nama_lengkap' => strtoupper($nama),
                    'tingkat'      => strtoupper(trim($row[2] ?? '')),
                    'jurusan'      => strtoupper(trim($row[3] ?? '')),
                    'rombel'       => strtoupper(trim($row[4] ?? '')),
                    'ruangan_id'   => null,
                    'created_at'   => date('Y-m-d H:i:s')
                ];

                $jumlahSukses++;
            }

            if (!empty($dataInsert)) {
                $this->db->table('siswa')->insertBatch($dataInsert);
            }

            $pesan = "Import Selesai! Berhasil: $jumlahSukses siswa.";
            if ($jumlahGagal > 0) $pesan .= " Gagal (NISN Duplikat): $jumlahGagal siswa.";

            return redirect()->back()->with('success', $pesan);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    public function mapel()
    {
        $mapel = $this->db->table('master_mapel')->orderBy('nama_mapel', 'ASC')->get()->getResultArray();
        $semua_guru = $this->db->table('staff')->where('role', 'guru')->orderBy('nama_lengkap', 'ASC')->get()->getResultArray();

        foreach ($mapel as &$m) {
            $m['guru_pengampu'] = $this->db->table('guru_mapel')
                ->select('staff.id, staff.nama_lengkap')
                ->join('staff', 'staff.id = guru_mapel.guru_id')
                ->where('mapel_id', $m['id'])
                ->get()->getResultArray();

            $m['total_pg'] = $this->db->table('bank_soal')
                ->where('mapel_id', $m['id'])
                ->where('jenis_soal', 'pg')
                ->countAllResults();

            $m['total_essai'] = $this->db->table('bank_soal')
                ->where('mapel_id', $m['id'])
                ->where('jenis_soal', 'essai')
                ->countAllResults();
        }

        $data = [
            'title'      => 'Manajemen Mata Pelajaran - CBT PRO',
            'mapel'      => $mapel,
            'semua_guru' => $semua_guru
        ];

        return view('panel/mapel', $data);
    }

    public function storeMapel()
    {
        $namaMapel = $this->request->getPost('nama_mapel');

        if ($this->db->table('master_mapel')->where('nama_mapel', $namaMapel)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Mata Pelajaran sudah ada!');
        }

        $this->db->table('master_mapel')->insert(['nama_mapel' => strtoupper($namaMapel)]);
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function deleteMapel($id)
    {
        $this->db->table('guru_mapel')->where('mapel_id', $id)->delete();
        $this->db->table('master_mapel')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Mata Pelajaran berhasil dihapus.');
    }

    public function syncGuruMapel()
    {
        $mapel_id = $this->request->getPost('mapel_id');
        $guru_ids = $this->request->getPost('guru_ids');

        $this->db->transStart();
        $this->db->table('guru_mapel')->where('mapel_id', $mapel_id)->delete();

        if (!empty($guru_ids)) {
            $dataInsert = [];
            foreach ($guru_ids as $g_id) {
                $dataInsert[] = ['guru_id' => $g_id, 'mapel_id' => $mapel_id];
            }
            $this->db->table('guru_mapel')->insertBatch($dataInsert);
        }
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memperbarui relasi Guru Pengampu.');
        }

        return redirect()->back()->with('success', 'Relasi Guru Pengampu berhasil diperbarui.');
    }

    public function jenisUjian()
    {
        $jenis_ujian = $this->db->table('master_jenis_ujian')->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title'       => 'Master Jenis Ujian - CBT PRO',
            'jenis_ujian' => $jenis_ujian
        ];

        return view('panel/jenis_ujian', $data);
    }

    public function storeJenisUjian()
    {
        $namaUjian = strtoupper($this->request->getPost('nama_ujian'));

        if ($this->db->table('master_jenis_ujian')->where('nama_ujian', $namaUjian)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Jenis Ujian tersebut sudah ada!');
        }

        $this->db->table('master_jenis_ujian')->insert(['nama_ujian' => $namaUjian]);
        return redirect()->back()->with('success', 'Jenis Ujian berhasil ditambahkan.');
    }

    public function updateJenisUjian($id)
    {
        $namaUjian = strtoupper($this->request->getPost('nama_ujian'));

        if ($this->db->table('master_jenis_ujian')->where('nama_ujian', $namaUjian)->where('id !=', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Gagal! Nama Jenis Ujian sudah dipakai.');
        }

        $this->db->table('master_jenis_ujian')->where('id', $id)->update(['nama_ujian' => $namaUjian]);
        return redirect()->back()->with('success', 'Jenis Ujian berhasil diperbarui.');
    }

    public function deleteJenisUjian($id)
    {
        $cekDipakai = $this->db->table('jadwal_ujian')->where('jenis_ujian_id', $id)->countAllResults();

        if ($cekDipakai > 0) {
            return redirect()->back()->with('error', 'Gagal Menghapus! Jenis Ujian ini sedang digunakan pada Jadwal Ujian yang sudah ada.');
        }

        $this->db->table('master_jenis_ujian')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Jenis Ujian berhasil dihapus.');
    }

    public function jadwal()
    {
        $search  = $this->request->getGet('search');
        $page    = (int)($this->request->getGet('page') ?? 1);
        if ($page < 1) $page = 1;
        $sortCol = $this->request->getGet('sort') ?? 'waktu';
        $sortDir = strtoupper($this->request->getGet('dir') ?? 'DESC');
        if (!in_array($sortDir, ['ASC', 'DESC'])) $sortDir = 'DESC';
        $allowedSorts = [
            'waktu'   => 'jadwal_ujian.waktu_mulai',
            'mapel'   => 'master_mapel.nama_mapel',
            'ruangan' => 'ruangan.nama_ruangan',
            'status'  => 'jadwal_ujian.status'
        ];
        $dbSortCol = $allowedSorts[$sortCol] ?? 'jadwal_ujian.waktu_mulai';
        $perPage = 25;
        $offset  = ($page - 1) * $perPage;
        $builderCount = $this->db->table('jadwal_ujian');
        if (!empty($search)) {
            $builderCount->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id', 'left');
            $builderCount->groupStart()
                ->like('master_mapel.nama_mapel', $search)
                ->orLike('jadwal_ujian.tingkat', $search)
                ->orLike('jadwal_ujian.jurusan', $search)
                ->groupEnd();
        }
        $totalData  = $builderCount->countAllResults();
        $totalPages = ceil($totalData / $perPage);
        $builderData = $this->db->table('jadwal_ujian')
            ->select('jadwal_ujian.*, master_jenis_ujian.nama_ujian, master_mapel.nama_mapel, ruangan.nama_ruangan, staff.nama_lengkap as nama_pengawas')
            ->join('master_jenis_ujian', 'master_jenis_ujian.id = jadwal_ujian.jenis_ujian_id', 'left')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id', 'left')
            ->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id', 'left')
            ->join('staff', 'staff.id = jadwal_ujian.pengawas_id', 'left');

        if (!empty($search)) {
            $builderData->groupStart()
                ->like('master_mapel.nama_mapel', $search)
                ->orLike('jadwal_ujian.tingkat', $search)
                ->orLike('jadwal_ujian.jurusan', $search)
                ->groupEnd();
        }
        $jadwal = $builderData->orderBy($dbSortCol, $sortDir)
            ->limit($perPage, $offset)
            ->get()->getResultArray();
        $currentTime = time();
        foreach ($jadwal as &$j) {
            if ($j['status'] === 'active') {
                $waktuSelesai = strtotime($j['waktu_selesai']);
                if ($currentTime > $waktuSelesai) {
                    $this->db->table('jadwal_ujian')->where('id', $j['id'])->update(['status' => 'finished']);
                    $j['status'] = 'finished';
                }
            }
        }
        $jenisUjian = $this->db->table('master_jenis_ujian')->get()->getResultArray();
        $mapel      = $this->db->table('master_mapel')->get()->getResultArray();
        $ruangan    = $this->db->table('ruangan')->get()->getResultArray();
        $semuaGuru  = $this->db->table('staff')->where('role', 'guru')->orderBy('nama_lengkap', 'ASC')->get()->getResultArray();

        foreach ($mapel as &$m) {
            $m['total_pg'] = $this->db->table('bank_soal')
                ->where('mapel_id', $m['id'])
                ->where('jenis_soal', 'pg')
                ->countAllResults();

            $m['total_essai'] = $this->db->table('bank_soal')
                ->where('mapel_id', $m['id'])
                ->where('jenis_soal', 'essai')
                ->countAllResults();
        }

        $data = [
            'title'       => 'Manajemen Jadwal - CBT PRO',
            'jadwal'      => $jadwal,
            'jenis_ujian' => $jenisUjian,
            'mapel'       => $mapel,
            'ruangan'     => $ruangan,
            'semua_guru'  => $semuaGuru,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'totalData'   => $totalData,
            'search'      => $search,
            'sortCol'     => $sortCol,
            'sortDir'     => $sortDir
        ];

        return view('panel/jadwal', $data);
    }

    public function storeJadwal()
    {
        $dataInsert = [
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'mapel_id'       => $this->request->getPost('mapel_id'),
            'tingkat'        => strtoupper($this->request->getPost('tingkat')),
            'jurusan'        => strtoupper($this->request->getPost('jurusan')),
            'ruangan_id'     => $this->request->getPost('ruangan_id'),
            'waktu_mulai'    => $this->request->getPost('waktu_mulai'),
            'waktu_selesai'  => $this->request->getPost('waktu_selesai'),
            'durasi'         => $this->request->getPost('durasi'),
            'status'         => 'draft',
            'pengawas_id'    => null
        ];

        $this->db->table('jadwal_ujian')->insert($dataInsert);
        return redirect()->back()->with('success', 'Kerangka jadwal berhasil dibuat. Silakan Plot Pengawas.');
    }

    public function updateJadwal($id)
    {
        $jadwal = $this->db->table('jadwal_ujian')->where('id', $id)->get()->getRowArray();

        $dataUpdate = [
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'mapel_id'       => $this->request->getPost('mapel_id'),
            'tingkat'        => strtoupper($this->request->getPost('tingkat')),
            'jurusan'        => strtoupper($this->request->getPost('jurusan')),
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
            } else if ($jadwal['status'] === 'finished' && strtotime($dataUpdate['waktu_selesai']) > time()) {
                $dataUpdate['status'] = 'ready';
            }
        }

        $this->db->table('jadwal_ujian')->where('id', $id)->update($dataUpdate);
        $pesan = $jadwal['status'] === 'active'
            ? 'Waktu ujian berhasil diperpanjang! (Data mapel/ruangan diabaikan karena ujian sedang berlangsung)'
            : 'Jadwal ujian berhasil diperbarui!';

        return redirect()->back()->with('success', $pesan);
    }

    public function deleteJadwal($id)
    {
        $jadwal = $this->db->table('jadwal_ujian')->where('id', $id)->get()->getRowArray();
        if ($jadwal['status'] === 'active') {
            return redirect()->back()->with('error', 'Akses Ditolak! Ujian sedang berlangsung, jadwal tidak boleh dihapus.');
        }

        $jsonPath = FCPATH . 'data_soal/jadwal_' . $id . '.json';
        if (file_exists($jsonPath)) unlink($jsonPath);

        $this->db->table('jadwal_ujian')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Jadwal dan file soal statis berhasil dihapus.');
    }

    public function plotPengawas()
    {
        $jadwalId   = $this->request->getPost('jadwal_id');
        $pengawasId = $this->request->getPost('pengawas_id');

        if (empty($pengawasId)) {
            $this->db->table('jadwal_ujian')->where('id', $jadwalId)->update(['pengawas_id' => null]);
            return redirect()->back()->with('success', 'Pengawas berhasil dilepas dari jadwal.');
        }

        $jadwalTarget = $this->db->table('jadwal_ujian')->where('id', $jadwalId)->get()->getRowArray();
        $startTarget  = strtotime($jadwalTarget['waktu_mulai']);
        $endTarget    = $startTarget + ($jadwalTarget['durasi'] * 60);

        $jadwalLain = $this->db->table('jadwal_ujian')
            ->where('pengawas_id', $pengawasId)
            ->where('id !=', $jadwalId)
            ->get()->getResultArray();

        foreach ($jadwalLain as $jl) {
            $startLain = strtotime($jl['waktu_mulai']);
            $endLain   = $startLain + ($jl['durasi'] * 60);

            if ($startTarget < $endLain && $endTarget > $startLain) {
                return redirect()->back()->with('error', 'BENTROK! Guru yang dipilih sudah mengawas di ruangan/jadwal lain pada jam tersebut.');
            }
        }

        $this->db->table('jadwal_ujian')->where('id', $jadwalId)->update(['pengawas_id' => $pengawasId]);
        return redirect()->back()->with('success', 'Pengawas berhasil di-plot tanpa bentrok!');
    }

    public function generateJson($jadwalId)
    {
        $jadwal = $this->db->table('jadwal_ujian')->where('id', $jadwalId)->get()->getRowArray();
        if (!$jadwal) return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');

        $soal = $this->db->table('bank_soal')->where('mapel_id', $jadwal['mapel_id'])->get()->getResultArray();

        if (empty($soal)) {
            return redirect()->back()->with('error', 'Gagal Generate! Bank soal untuk mata pelajaran ini masih kosong.');
        }

        $soalClean = [];
        foreach ($soal as $s) {
            unset($s['kunci_jawaban']);
            $soalClean[] = $s;
        }

        $jsonContent = json_encode($soalClean);
        $filePath    = FCPATH . 'data_soal/jadwal_' . $jadwalId . '.json';

        if (file_put_contents($filePath, $jsonContent)) {
            $this->db->table('jadwal_ujian')->where('id', $jadwalId)->update(['status' => 'ready']);
            return redirect()->back()->with('success', 'Engine Ready! File JSON statis berhasil di-generate.');
        }

        return redirect()->back()->with('error', 'Gagal menulis file JSON. Pastikan folder public/data_soal memiliki izin tulis (chmod 777).');
    }

    public function cetakKartu()
    {
        $siswa = $this->db->table('siswa')
            ->select('siswa.*, ruangan.nama_ruangan')
            ->join('ruangan', 'ruangan.id = siswa.ruangan_id', 'left')
            ->orderBy('ruangan.nama_ruangan', 'ASC')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()->getResultArray();

        $staff = $this->db->table('staff')
            ->orderBy('role', 'ASC')
            ->orderBy('nama_lengkap', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Cetak Kartu Ujian - CBT PRO',
            'siswa' => $siswa,
            'staff' => $staff
        ];

        return view('panel/cetak_kartu', $data);
    }
}
