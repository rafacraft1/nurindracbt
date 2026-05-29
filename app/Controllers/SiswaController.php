<?php

namespace App\Controllers;

use App\Models\SiswaModel;
use App\Services\ExcelService;
use CodeIgniter\HTTP\ResponseInterface;

class SiswaController extends BaseController
{
    protected SiswaModel $siswaModel;
    protected ExcelService $excelService;

    public function __construct()
    {
        $this->siswaModel   = new SiswaModel();
        $this->excelService = new ExcelService();
    }

    public function index(): string
    {
        $search  = $this->request->getGet('search');
        $page    = (int)($this->request->getGet('page') ?? 1);
        $page    = max($page, 1);

        $sortCol = (string)($this->request->getGet('sort') ?? 'kelas');
        $sortDir = strtoupper((string)($this->request->getGet('dir') ?? 'ASC'));
        $sortDir = in_array($sortDir, ['ASC', 'DESC']) ? $sortDir : 'ASC';

        $perPage = 50;
        $offset  = ($page - 1) * $perPage;

        $totalData  = $this->siswaModel->countTotalSiswa($search);
        $totalPages = (int)ceil($totalData / $perPage);
        $siswa      = $this->siswaModel->getPaginatedSiswa($search, $sortCol, $sortDir, $perPage, $offset);

        $db = \Config\Database::connect();
        $ruangan = $db->table('ruangan')->get()->getResultArray();

        $data = [
            'title'       => 'Data Siswa - CBT PRO',
            'siswa'       => $siswa,
            'ruangan'     => $ruangan,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'totalData'   => $totalData,
            'search'      => $search,
            'sortCol'     => $sortCol,
            'sortDir'     => $sortDir
        ];

        return view('panel/siswa', $data);
    }

    public function store(): ResponseInterface
    {
        $nisn = (string)$this->request->getPost('nisn');

        if ($this->siswaModel->isNisnExist($nisn)) {
            return redirect()->back()->with('error', 'NISN sudah terdaftar di sistem!');
        }

        $passwordPlain = (string)$this->request->getPost('password') ?: 'siswa123';

        $this->siswaModel->insert([
            'nisn'           => $nisn,
            'password'       => password_hash($passwordPlain, PASSWORD_DEFAULT),
            'password_plain' => $passwordPlain,
            'nama_lengkap'   => strtoupper((string)$this->request->getPost('nama_lengkap')),
            'tingkat'        => strtoupper((string)$this->request->getPost('tingkat')),
            'jurusan'        => strtoupper((string)$this->request->getPost('jurusan')),
            'rombel'         => strtoupper((string)$this->request->getPost('rombel')),
            'ruangan_id'     => $this->request->getPost('ruangan_id') ?: null,
            'created_at'     => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function update(string $id): ResponseInterface
    {
        $dataUpdate = [
            'nama_lengkap' => strtoupper((string)$this->request->getPost('nama_lengkap')),
            'tingkat'      => strtoupper((string)$this->request->getPost('tingkat')),
            'jurusan'      => strtoupper((string)$this->request->getPost('jurusan')),
            'rombel'       => strtoupper((string)$this->request->getPost('rombel')),
            'ruangan_id'   => $this->request->getPost('ruangan_id') ?: null,
        ];

        $passwordBaru = (string)$this->request->getPost('password');
        if (!empty($passwordBaru)) {
            $dataUpdate['password']       = password_hash($passwordBaru, PASSWORD_DEFAULT);
            $dataUpdate['password_plain'] = $passwordBaru;
        }

        $this->siswaModel->update($id, $dataUpdate);
        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function delete(string $id): ResponseInterface
    {
        $this->siswaModel->delete($id);
        return redirect()->back()->with('success', 'Data siswa berhasil dihapus.');
    }

    public function deleteBatch(): ResponseInterface
    {
        $ids = $this->request->getPost('ids');
        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tidak ada data siswa yang dipilih.',
                'csrf'    => csrf_hash()
            ]);
        }

        $this->siswaModel->whereIn('id', $ids)->delete();

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => count($ids) . ' data siswa berhasil dihapus secara permanen.',
            'csrf'    => csrf_hash()
        ]);
    }

    public function import(): ResponseInterface
    {
        $step = (string)($this->request->getPost('step') ?? 'init');

        // ==============================================================================
        // TAHAP 1: INIT - Konversi ke JSONL (Baris per Baris)
        // ==============================================================================
        if ($step === 'init') {
            $file = $this->request->getFile('file_excel');

            if (!$file || !$file->isValid() || $file->hasMoved()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Pilih file Excel yang valid!', 'csrf' => csrf_hash()]);
            }

            if (!in_array($file->getClientExtension(), ['xls', 'xlsx'])) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Format file harus .xls atau .xlsx', 'csrf' => csrf_hash()]);
            }

            try {
                $cleanRows = $this->excelService->parseSiswaExcel($file);

                if (empty($cleanRows)) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan di dalam Excel.', 'csrf' => csrf_hash()]);
                }

                $tempId = uniqid('import_');
                $filePath = WRITEPATH . 'uploads/' . $tempId . '.jsonl';
                $handle = fopen($filePath, 'w');
                foreach ($cleanRows as $row) {
                    fwrite($handle, json_encode($row) . PHP_EOL);
                }
                fclose($handle);

                return $this->response->setJSON([
                    'status'  => 'success',
                    'temp_id' => $tempId,
                    'total'   => count($cleanRows),
                    'csrf'    => csrf_hash()
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage(), 'csrf' => csrf_hash()]);
            }
        }

        // ==============================================================================
        // TAHAP 2: PROCESS - Streaming Data, Password Cache & Bulk Insert
        // ==============================================================================
        if ($step === 'process') {
            set_time_limit(0);

            $tempId = (string)$this->request->getPost('temp_id');
            $offset = (int)$this->request->getPost('offset');
            $limit  = (int)$this->request->getPost('limit');

            $filePath = WRITEPATH . 'uploads/' . $tempId . '.jsonl';
            if (!file_exists($filePath)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'File temporary tidak ditemukan. Muat ulang halaman.', 'csrf' => csrf_hash()]);
            }

            // OPTIMASI: Membaca file hanya pada baris yang dibutuhkan (Seek)
            $file = new \SplFileObject($filePath);
            $file->seek($offset);

            $chunk = [];
            for ($i = 0; $i < $limit; $i++) {
                if ($file->eof()) break;
                $line = $file->current();
                if (!empty(trim($line))) {
                    $chunk[] = json_decode($line, true);
                }
                $file->next();
            }

            // Filter Anti-Duplikat (Hanya melacak NISN di chunk ini)
            $nisnList = [];
            foreach ($chunk as $row) {
                $nisnVal = trim((string)($row[0] ?? ''));
                if (!empty($nisnVal)) $nisnList[] = $nisnVal;
            }

            $existingMap = [];
            if (!empty($nisnList)) {
                $existingDb = $this->siswaModel->select('nisn')->whereIn('nisn', $nisnList)->findAll();
                foreach ($existingDb as $rowDb) {
                    $existingMap[$rowDb['nisn']] = true;
                }
            }

            $dataInsert    = [];
            $jumlahSukses  = 0;
            $jumlahGagal   = 0;
            $passwordCache = [];

            foreach ($chunk as $row) {
                $nisn = trim((string)($row[0] ?? ''));
                $nama = trim((string)($row[1] ?? ''));

                if (empty($nisn) || isset($existingMap[$nisn])) {
                    $jumlahGagal++;
                    continue;
                }

                $passwordPlain = trim((string)($row[5] ?? '')) ?: 'siswa123';

                // OPTIMASI: Caching Hash memori sementara
                if (!isset($passwordCache[$passwordPlain])) {
                    $passwordCache[$passwordPlain] = password_hash($passwordPlain, PASSWORD_DEFAULT);
                }

                $dataInsert[] = [
                    'nisn'           => $nisn,
                    'password'       => $passwordCache[$passwordPlain],
                    'password_plain' => $passwordPlain,
                    'nama_lengkap'   => strtoupper($nama),
                    'tingkat'        => strtoupper(trim((string)($row[2] ?? ''))),
                    'jurusan'        => strtoupper(trim((string)($row[3] ?? ''))),
                    'rombel'         => strtoupper(trim((string)($row[4] ?? ''))),
                    'ruangan_id'     => null,
                    'created_at'     => date('Y-m-d H:i:s')
                ];

                $existingMap[$nisn] = true;
                $jumlahSukses++;
            }

            if (!empty($dataInsert)) {
                $this->siswaModel->insertBatch($dataInsert);
            }

            // Garbage collection manual
            unset($chunk, $dataInsert, $existingMap, $passwordCache, $file);

            return $this->response->setJSON([
                'status' => 'success',
                'sukses' => $jumlahSukses,
                'gagal'  => $jumlahGagal,
                'csrf'   => csrf_hash()
            ]);
        }

        // ==============================================================================
        // TAHAP 3: FINISH - Membersihkan File
        // ==============================================================================
        if ($step === 'finish') {
            $tempId = (string)$this->request->getPost('temp_id');
            $filePath = WRITEPATH . 'uploads/' . $tempId . '.jsonl';
            if (file_exists($filePath)) unlink($filePath);

            return $this->response->setJSON(['status' => 'success', 'csrf' => csrf_hash()]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid Step', 'csrf' => csrf_hash()]);
    }

    public function cetakKartu(): string
    {
        $db = \Config\Database::connect();

        $tingkat = $db->table('siswa')->select('tingkat')->distinct()->orderBy('tingkat', 'ASC')->get()->getResultArray();
        $jurusan = $db->table('siswa')->select('jurusan')->distinct()->orderBy('jurusan', 'ASC')->get()->getResultArray();
        $rombel  = $db->table('siswa')->select('rombel')->distinct()->orderBy('rombel', 'ASC')->get()->getResultArray();

        $filterTingkat = $this->request->getGet('tingkat');
        $filterJurusan = $this->request->getGet('jurusan');
        $filterRombel  = $this->request->getGet('rombel');
        $cetakIds      = $this->request->getGet('cetak_ids');

        $builderSiswa = $db->table('siswa')
            ->select('siswa.*, ruangan.nama_ruangan')
            ->join('ruangan', 'ruangan.id = siswa.ruangan_id', 'left')
            ->orderBy('siswa.tingkat', 'ASC')
            ->orderBy('siswa.jurusan', 'ASC')
            ->orderBy('siswa.rombel', 'ASC')
            ->orderBy('siswa.nama_lengkap', 'ASC');

        if (!empty($filterTingkat)) $builderSiswa->where('siswa.tingkat', $filterTingkat);
        if (!empty($filterJurusan)) $builderSiswa->where('siswa.jurusan', $filterJurusan);
        if (!empty($filterRombel))  $builderSiswa->where('siswa.rombel', $filterRombel);

        if (!empty($cetakIds)) {
            $idsArray = explode(',', (string)$cetakIds);
            $builderSiswa->whereIn('siswa.id', $idsArray);
        }

        $data = [
            'title'         => 'Cetak Kartu Ujian - CBT PRO',
            'siswa'         => $builderSiswa->get()->getResultArray(),
            'staff'         => $db->table('staff')->orderBy('role', 'ASC')->orderBy('nama_lengkap', 'ASC')->get()->getResultArray(),
            'pengaturan'    => $this->pengaturanGlobal,
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
