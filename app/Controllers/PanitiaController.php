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
    /**
     * @var \CodeIgniter\Database\BaseConnection
     */
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

    public function deleteRuangan(string $id)
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

    public function kosongkanRuangan(string $id)
    {
        $this->db->table('siswa')->where('ruangan_id', $id)->update(['ruangan_id' => null]);
        return redirect()->back()->with('success', 'Ruangan berhasil dikosongkan.');
    }

    public function siswa()
    {
        $search  = $this->request->getGet('search');
        $page    = (int)($this->request->getGet('page') ?? 1);
        if ($page < 1) $page = 1;

        // Tangkap parameter sorting (Default: urut berdasarkan Kelas)
        $sortCol = $this->request->getGet('sort') ?? 'kelas';
        $sortDir = strtoupper($this->request->getGet('dir') ?? 'ASC');
        if (!in_array($sortDir, ['ASC', 'DESC'])) $sortDir = 'ASC';

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

        // Terapkan logika pengurutan dinamis
        if ($sortCol === 'nisn') {
            $builderData->orderBy('nisn', $sortDir);
        } elseif ($sortCol === 'nama') {
            $builderData->orderBy('nama_lengkap', $sortDir);
        } else {
            // Urutan default (Berdasarkan Tingkat -> Jurusan -> Rombel)
            $sortCol = 'kelas';
            $builderData->orderBy('tingkat', $sortDir)
                ->orderBy('jurusan', $sortDir)
                ->orderBy('rombel', $sortDir)
                ->orderBy('nama_lengkap', 'ASC');
        }

        $siswa = $builderData->limit($perPage, $offset)->get()->getResultArray();

        $ruangan = $this->db->table('ruangan')->get()->getResultArray();

        $data = [
            'title'       => 'Data Siswa - CBT PRO',
            'siswa'       => $siswa,
            'ruangan'     => $ruangan,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'totalData'   => $totalData,
            'search'      => $search,
            'sortCol'     => $sortCol, // Lempar param ke view
            'sortDir'     => $sortDir  // Lempar param ke view
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
            'nisn'           => $nisn,
            'password'       => password_hash($passwordPlain, PASSWORD_DEFAULT),
            'password_plain' => $passwordPlain,
            'nama_lengkap'   => strtoupper($this->request->getPost('nama_lengkap')),
            'tingkat'        => strtoupper($this->request->getPost('tingkat')),
            'jurusan'        => strtoupper($this->request->getPost('jurusan')),
            'rombel'         => strtoupper($this->request->getPost('rombel')),
            'ruangan_id'     => $this->request->getPost('ruangan_id') ?: null,
            'created_at'     => date('Y-m-d H:i:s')
        ];

        $this->db->table('siswa')->insert($dataInsert);
        return redirect()->back()->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function updateSiswa(string $id)
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
            $dataUpdate['password']       = password_hash($passwordBaru, PASSWORD_DEFAULT);
            $dataUpdate['password_plain'] = $passwordBaru;
        }

        $this->db->table('siswa')->where('id', $id)->update($dataUpdate);
        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function deleteSiswa(string $id)
    {
        $this->db->table('siswa')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Data siswa berhasil dihapus.');
    }

    public function deleteSiswaBatch()
    {
        $ids = $this->request->getPost('ids');
        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tidak ada data siswa yang dipilih.',
                'csrf' => csrf_hash()
            ]);
        }

        $this->db->table('siswa')->whereIn('id', $ids)->delete();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => count($ids) . ' data siswa berhasil dihapus secara permanen.',
            'csrf' => csrf_hash()
        ]);
    }

    public function importSiswa()
    {
        $step = $this->request->getPost('step') ?? 'init';

        if ($step === 'init') {
            $file = $this->request->getFile('file_excel');

            if (!$file || !$file->isValid() || $file->hasMoved()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Pilih file Excel yang valid!', 'csrf' => csrf_hash()]);
            }

            $extension = $file->getClientExtension();
            if (!in_array($extension, ['xls', 'xlsx'])) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Format file harus .xls atau .xlsx', 'csrf' => csrf_hash()]);
            }

            try {
                $spreadsheet = IOFactory::load($file->getTempName());
                $sheet       = $spreadsheet->getActiveSheet();
                $rows        = $sheet->toArray();

                $cleanRows = [];
                foreach ($rows as $key => $row) {
                    if ($key == 0) continue;
                    $nisn = trim($row[0] ?? '');
                    $nama = trim($row[1] ?? '');
                    if (empty($nisn) || empty($nama)) continue;
                    $cleanRows[] = $row;
                }

                if (empty($cleanRows)) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan di dalam Excel.', 'csrf' => csrf_hash()]);
                }

                $tempId = uniqid('import_');
                $filePath = WRITEPATH . 'uploads/' . $tempId . '.json';
                file_put_contents($filePath, json_encode($cleanRows));

                return $this->response->setJSON([
                    'status'  => 'success',
                    'temp_id' => $tempId,
                    'total'   => count($cleanRows),
                    'csrf'    => csrf_hash()
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal membaca file: ' . $e->getMessage(), 'csrf' => csrf_hash()]);
            }
        }

        if ($step === 'process') {
            set_time_limit(0);
            ini_set('memory_limit', '512M');

            $tempId = $this->request->getPost('temp_id');
            $offset = (int) $this->request->getPost('offset');
            $limit  = (int) $this->request->getPost('limit');

            $filePath = WRITEPATH . 'uploads/' . $tempId . '.json';
            if (!file_exists($filePath)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'File temporary tidak ditemukan. Muat ulang halaman.', 'csrf' => csrf_hash()]);
            }

            $rows = json_decode(file_get_contents($filePath), true);
            $chunk = array_slice($rows, $offset, $limit);

            $dataInsert   = [];
            $jumlahSukses = 0;
            $jumlahGagal  = 0;

            $existingDb = $this->db->table('siswa')->select('nisn')->get()->getResultArray();
            $existingMap = [];
            foreach ($existingDb as $rowDb) {
                $existingMap[$rowDb['nisn']] = true;
            }

            $passwordCache = [];
            $passwordCache['siswa123'] = password_hash('siswa123', PASSWORD_DEFAULT);

            foreach ($chunk as $row) {
                $nisn = trim($row[0] ?? '');
                $nama = trim($row[1] ?? '');

                if (isset($existingMap[$nisn])) {
                    $jumlahGagal++;
                    continue;
                }

                $passwordPlain = trim($row[5] ?? '') ?: 'siswa123';

                if (!isset($passwordCache[$passwordPlain])) {
                    $passwordCache[$passwordPlain] = password_hash($passwordPlain, PASSWORD_DEFAULT);
                }

                $dataInsert[] = [
                    'nisn'           => $nisn,
                    'password'       => $passwordCache[$passwordPlain],
                    'password_plain' => $passwordPlain,
                    'nama_lengkap'   => strtoupper($nama),
                    'tingkat'        => strtoupper(trim($row[2] ?? '')),
                    'jurusan'        => strtoupper(trim($row[3] ?? '')),
                    'rombel'         => strtoupper(trim($row[4] ?? '')),
                    'ruangan_id'     => null,
                    'created_at'     => date('Y-m-d H:i:s')
                ];

                $existingMap[$nisn] = true;
                $jumlahSukses++;
            }

            if (!empty($dataInsert)) {
                $this->db->table('siswa')->insertBatch($dataInsert);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'sukses' => $jumlahSukses,
                'gagal'  => $jumlahGagal,
                'csrf'   => csrf_hash()
            ]);
        }

        if ($step === 'finish') {
            $tempId = $this->request->getPost('temp_id');
            $filePath = WRITEPATH . 'uploads/' . $tempId . '.json';

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'csrf'   => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid Step', 'csrf' => csrf_hash()]);
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

    public function updateMapel(string $id)
    {
        $namaMapel = strtoupper($this->request->getPost('nama_mapel'));

        if ($this->db->table('master_mapel')->where('nama_mapel', $namaMapel)->where('id !=', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Gagal! Nama Mata Pelajaran sudah dipakai.');
        }

        $this->db->table('master_mapel')->where('id', $id)->update(['nama_mapel' => $namaMapel]);
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function deleteMapel(string $id)
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

    public function updateJenisUjian(string $id)
    {
        $namaUjian = strtoupper($this->request->getPost('nama_ujian'));

        if ($this->db->table('master_jenis_ujian')->where('nama_ujian', $namaUjian)->where('id !=', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Gagal! Nama Jenis Ujian sudah dipakai.');
        }

        $this->db->table('master_jenis_ujian')->where('id', $id)->update(['nama_ujian' => $namaUjian]);
        return redirect()->back()->with('success', 'Jenis Ujian berhasil diperbarui.');
    }

    public function deleteJenisUjian(string $id)
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

    public function updateJadwal(string $id)
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

    public function deleteJadwal(string $id)
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

    public function generateJson(string $jadwalId)
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

    // =========================================================
    // FUNGSI CETAK KARTU YANG SUDAH DIREVISI
    // =========================================================
    public function cetakKartu()
    {
        // 1. Ambil data untuk Filter Dropdown (Tingkat, Jurusan, Rombel)
        $tingkat = $this->db->table('siswa')->select('tingkat')->distinct()->orderBy('tingkat', 'ASC')->get()->getResultArray();
        $jurusan = $this->db->table('siswa')->select('jurusan')->distinct()->orderBy('jurusan', 'ASC')->get()->getResultArray();
        $rombel  = $this->db->table('siswa')->select('rombel')->distinct()->orderBy('rombel', 'ASC')->get()->getResultArray();

        // 2. Tangkap parameter filter GET
        $filterTingkat = $this->request->getGet('tingkat');
        $filterJurusan = $this->request->getGet('jurusan');
        $filterRombel  = $this->request->getGet('rombel');
        $cetakIds      = $this->request->getGet('cetak_ids'); // Parameter ID untuk cetak spesifik

        // 3. Query Builder Siswa
        $builderSiswa = $this->db->table('siswa')
            ->select('siswa.*, ruangan.nama_ruangan')
            ->join('ruangan', 'ruangan.id = siswa.ruangan_id', 'left')
            ->orderBy('siswa.tingkat', 'ASC')
            ->orderBy('siswa.jurusan', 'ASC')
            ->orderBy('siswa.rombel', 'ASC')
            ->orderBy('siswa.nama_lengkap', 'ASC');

        // 4. Terapkan Filter (Jika user memilih dropdown)
        if (!empty($filterTingkat)) {
            $builderSiswa->where('siswa.tingkat', $filterTingkat);
        }
        if (!empty($filterJurusan)) {
            $builderSiswa->where('siswa.jurusan', $filterJurusan);
        }
        if (!empty($filterRombel)) {
            $builderSiswa->where('siswa.rombel', $filterRombel);
        }

        // 5. Terapkan Filter Cetak Spesifik (Jika user menceklis kartu)
        if (!empty($cetakIds)) {
            $idsArray = explode(',', $cetakIds);
            $builderSiswa->whereIn('siswa.id', $idsArray);
        }

        $siswa = $builderSiswa->get()->getResultArray();

        $staff = $this->db->table('staff')
            ->orderBy('role', 'ASC')
            ->orderBy('nama_lengkap', 'ASC')
            ->get()->getResultArray();

        $pengaturan = $this->db->table('pengaturan')->where('id', 1)->get()->getRowArray();

        $data = [
            'title'         => 'Cetak Kartu Ujian - CBT PRO',
            'siswa'         => $siswa,
            'staff'         => $staff,
            'pengaturan'    => $pengaturan,

            // Kirim variabel filter ke view
            'listTingkat'   => $tingkat,
            'listJurusan'   => $jurusan,
            'listRombel'    => $rombel,
            'filterTingkat' => $filterTingkat,
            'filterJurusan' => $filterJurusan,
            'filterRombel'  => $filterRombel,
        ];

        return view('panel/cetak_kartu', $data);
    }
}
