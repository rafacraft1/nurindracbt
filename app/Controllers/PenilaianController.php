<?php

namespace App\Controllers;

use App\Models\JadwalModel;
use App\Models\HasilUjianModel;
use App\Models\BankSoalModel;
use App\Models\JenisUjianModel;
use App\Models\RuanganModel;
use App\Services\ExcelService;
use CodeIgniter\HTTP\ResponseInterface;

class PenilaianController extends BaseController
{
    protected JadwalModel $jadwalModel;
    protected HasilUjianModel $hasilUjianModel;
    protected BankSoalModel $bankSoalModel;
    protected ExcelService $excelService;

    public function __construct()
    {
        $this->jadwalModel     = new JadwalModel();
        $this->hasilUjianModel = new HasilUjianModel();
        $this->bankSoalModel   = new BankSoalModel();
        $this->excelService    = new ExcelService();
    }

    public function index(): string
    {
        $role   = (string)session()->get('role');
        $guruId = (string)session()->get('id');

        $builder = $this->jadwalModel->select('jadwal_ujian.*, master_mapel.nama_mapel, ruangan.nama_ruangan')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id', 'left')
            ->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id', 'left')
            ->whereIn('jadwal_ujian.status', ['ready', 'active', 'finished'])
            ->where('jadwal_ujian.tahun_ajaran', $this->tahunAktif)
            ->where('jadwal_ujian.semester', $this->smtAktif);

        if ($role === 'guru') {
            $db = \Config\Database::connect();
            $mapelGuru = $db->table('guru_mapel')->where('guru_id', $guruId)->get()->getResultArray();
            $mapelIds  = array_column($mapelGuru, 'mapel_id');

            if (!empty($mapelIds)) {
                $builder->whereIn('jadwal_ujian.mapel_id', $mapelIds);
            } else {
                $builder->where('1=0');
            }
        }

        $jenisUjianModel = new JenisUjianModel();
        $ruanganModel    = new RuanganModel();
        $db = \Config\Database::connect();

        $data = [
            'title'       => 'Laporan Nilai Ujian - CBT PRO',
            'jadwal'      => $builder->orderBy('jadwal_ujian.waktu_mulai', 'DESC')->findAll(),
            'jenis_ujian' => $jenisUjianModel->findAll(),
            'ruangan'     => $ruanganModel->findAll(),
            'semua_guru'  => $db->table('staff')->where('role', 'guru')->orderBy('nama_lengkap', 'ASC')->get()->getResultArray()
        ];

        return view('panel/penilaian/index', $data);
    }

    public function detail(string $jadwalId): ResponseInterface|string
    {
        $jadwalRef = $this->jadwalModel->select('jadwal_ujian.*, master_mapel.nama_mapel, ruangan.nama_ruangan')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id', 'left')
            ->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id', 'left')
            ->find($jadwalId);

        if (!$jadwalRef) return redirect()->to('/panel/penilaian')->with('error', 'Jadwal tidak ditemukan.');

        if (!$this->verifyMapelAccess($jadwalRef['mapel_id'])) {
            return redirect()->to('/panel/penilaian')->with('error', 'Akses Ditolak! Anda bukan pengampu mata pelajaran ini.');
        }

        $rombelFilter = $this->request->getGet('rombel');

        $semuaJadwalSerumpun = $this->jadwalModel->where('mapel_id', $jadwalRef['mapel_id'])
            ->where('tingkat', $jadwalRef['tingkat'])
            ->where('jurusan', $jadwalRef['jurusan'])
            ->where('tahun_ajaran', $this->tahunAktif)
            ->where('semester', $this->smtAktif)
            ->findAll();

        $arrJadwalIds = array_column($semuaJadwalSerumpun, 'id');
        if (empty($arrJadwalIds)) $arrJadwalIds = [0];

        $db = \Config\Database::connect();

        $listRombel = $db->table('siswa')
            ->select('rombel')
            ->where('tingkat', $jadwalRef['tingkat'])
            ->where('jurusan', $jadwalRef['jurusan'])
            ->where('rombel !=', '')
            ->groupBy('rombel')
            ->orderBy('rombel', 'ASC')
            ->get()->getResultArray();

        $builderSiswa = $db->table('siswa')
            ->select('siswa.id, siswa.nisn, siswa.nama_lengkap, siswa.tingkat, siswa.jurusan, siswa.rombel, hasil_ujian.nilai_pg, hasil_ujian.nilai_essai, hasil_ujian.status, master_jenis_ujian.nama_ujian as keterangan_ujian, hasil_ujian.jadwal_id as actual_jadwal_id')
            ->join('hasil_ujian', "hasil_ujian.siswa_id = siswa.id AND hasil_ujian.jadwal_id IN (" . implode(',', $arrJadwalIds) . ")", 'left')
            ->join('jadwal_ujian', 'jadwal_ujian.id = hasil_ujian.jadwal_id', 'left')
            ->join('master_jenis_ujian', 'master_jenis_ujian.id = jadwal_ujian.jenis_ujian_id', 'left')
            ->where('siswa.tingkat', $jadwalRef['tingkat'])
            ->where('siswa.jurusan', $jadwalRef['jurusan']);

        if (!empty($rombelFilter)) {
            $builderSiswa->where('siswa.rombel', $rombelFilter);
        }

        $siswa = $builderSiswa->orderBy('siswa.nama_lengkap', 'ASC')->get()->getResultArray();

        $data = [
            'title'        => 'Detail Nilai: ' . $jadwalRef['nama_mapel'],
            'jadwal'       => $jadwalRef,
            'siswa'        => $siswa,
            'listRombel'   => $listRombel,
            'rombelFilter' => $rombelFilter
        ];

        return view('panel/penilaian/detail', $data);
    }

    public function koreksi(string $jadwalId, string $siswaId): ResponseInterface|string
    {
        $jadwal = $this->jadwalModel->find($jadwalId);
        if (!$jadwal) return redirect()->to('/panel/penilaian')->with('error', 'Jadwal tidak ditemukan.');

        if (!$this->verifyMapelAccess($jadwal['mapel_id'])) {
            return redirect()->to('/panel/penilaian')->with('error', 'Akses Ditolak!');
        }

        $hasil  = $this->hasilUjianModel->getHasilByJadwalSiswa($jadwalId, $siswaId);
        if (!$hasil || empty($hasil['jawaban_peserta'])) {
            return redirect()->back()->with('error', 'Siswa belum mengerjakan atau belum mengumpulkan ujian.');
        }

        $db = \Config\Database::connect();
        $siswa = $db->table('siswa')->where('id', $siswaId)->get()->getRowArray();
        $soalEssai = $this->bankSoalModel->where('mapel_id', $jadwal['mapel_id'])->where('jenis_soal', 'essai')->findAll();

        $data = [
            'title'        => 'Koreksi Essai: ' . $siswa['nama_lengkap'],
            'jadwal'       => $jadwal,
            'siswa'        => $siswa,
            'hasil'        => $hasil,
            'soal_essai'   => $soalEssai,
            'jawaban_json' => json_decode((string)$hasil['jawaban_peserta'], true),
            'rombelFilter' => $this->request->getGet('rombel')
        ];

        return view('panel/penilaian/koreksi', $data);
    }

    public function simpanKoreksi(): ResponseInterface
    {
        $jadwalId   = (string)$this->request->getPost('jadwal_id');
        $siswaId    = (string)$this->request->getPost('siswa_id');
        $nilaiEssai = (float)$this->request->getPost('nilai_essai');
        $rombelFilter = $this->request->getPost('rombel');

        $jadwal = $this->jadwalModel->find($jadwalId);
        if (!$jadwal || !$this->verifyMapelAccess($jadwal['mapel_id'])) {
            return redirect()->to('/panel/penilaian')->with('error', 'Akses Ilegal!');
        }

        $hasil = $this->hasilUjianModel->getHasilByJadwalSiswa($jadwalId, $siswaId);
        if ($hasil) {
            $this->hasilUjianModel->update($hasil['id'], ['nilai_essai' => $nilaiEssai]);
        }

        $redirectUrl = "/panel/penilaian/detail/$jadwalId";
        if (!empty($rombelFilter)) {
            $redirectUrl .= '?rombel=' . urlencode((string)$rombelFilter);
        }

        return redirect()->to($redirectUrl)->with('success', 'Nilai Essai berhasil disimpan!');
    }

    public function exportExcel(string $jadwalId)
    {
        $jadwalRef = $this->jadwalModel->select('jadwal_ujian.*, master_mapel.nama_mapel')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id', 'left')
            ->find($jadwalId);

        if (!$jadwalRef) return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');

        if (!$this->verifyMapelAccess($jadwalRef['mapel_id'])) {
            return redirect()->to('/panel/penilaian')->with('error', 'Otoritas Gagal!');
        }

        $rombelFilter = $this->request->getGet('rombel');

        $semuaJadwalSerumpun = $this->jadwalModel->where('mapel_id', $jadwalRef['mapel_id'])
            ->where('tingkat', $jadwalRef['tingkat'])
            ->where('jurusan', $jadwalRef['jurusan'])
            ->where('tahun_ajaran', $this->tahunAktif)
            ->where('semester', $this->smtAktif)
            ->findAll();

        $arrJadwalIds = array_column($semuaJadwalSerumpun, 'id');
        if (empty($arrJadwalIds)) $arrJadwalIds = [0];

        $db = \Config\Database::connect();
        $builderSiswa = $db->table('siswa')
            ->select('siswa.*, hasil_ujian.nilai_pg, hasil_ujian.nilai_essai, hasil_ujian.status, master_jenis_ujian.nama_ujian as keterangan_ujian')
            ->join('hasil_ujian', "hasil_ujian.siswa_id = siswa.id AND hasil_ujian.jadwal_id IN (" . implode(',', $arrJadwalIds) . ")", 'left')
            ->join('jadwal_ujian', 'jadwal_ujian.id = hasil_ujian.jadwal_id', 'left')
            ->join('master_jenis_ujian', 'master_jenis_ujian.id = jadwal_ujian.jenis_ujian_id', 'left')
            ->where('siswa.tingkat', $jadwalRef['tingkat'])
            ->where('siswa.jurusan', $jadwalRef['jurusan']);

        if (!empty($rombelFilter)) {
            $builderSiswa->where('siswa.rombel', $rombelFilter);
        }

        $siswa = $builderSiswa->orderBy('siswa.nama_lengkap', 'ASC')->get()->getResultArray();

        $spreadsheet = $this->excelService->buildRekapNilaiExcel($jadwalRef, $siswa);
        $writer      = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $namaRombelStr = !empty($rombelFilter) ? '_' . str_replace(' ', '_', $rombelFilter) : '_SEMUA_KELAS';
        $fileName    = 'Nilai_' . str_replace(' ', '_', $jadwalRef['nama_mapel']) . '_' . $jadwalRef['tingkat'] . '_' . $jadwalRef['jurusan'] . $namaRombelStr . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }

    public function createSusulanGabungan(): ResponseInterface
    {
        $mapelId      = $this->request->getPost('mapel_id');
        $tingkat      = $this->request->getPost('tingkat');
        $jenisUjianId = $this->request->getPost('jenis_ujian_id');
        $ruanganId    = $this->request->getPost('ruangan_id');
        $pengawasId   = $this->request->getPost('pengawas_id');

        // FIX: Konversi Format DateTime-Local
        $waktuMulai   = date('Y-m-d H:i:s', strtotime((string)$this->request->getPost('waktu_mulai')));
        $waktuSelesai = date('Y-m-d H:i:s', strtotime((string)$this->request->getPost('waktu_selesai')));

        $durasi       = (int)$this->request->getPost('durasi');

        if (!$this->verifyMapelAccess($mapelId)) {
            return redirect()->back()->with('error', 'Akses Ditolak! Anda bukan pengampu mapel ini.');
        }

        $jadwalBelumSelesai = $this->jadwalModel
            ->where('mapel_id', $mapelId)
            ->where('tingkat', $tingkat)
            ->whereIn('status', ['draft', 'ready', 'active'])
            ->where('tahun_ajaran', $this->tahunAktif)
            ->where('semester', $this->smtAktif)
            ->countAllResults();

        if ($jadwalBelumSelesai > 0) {
            return redirect()->back()->with('error', 'Gagal! Masih ada kelas reguler yang belum selesai ujian, atau jadwal susulan sebelumnya masih aktif.');
        }

        $db = \Config\Database::connect();

        $jadwalReguler = $this->jadwalModel
            ->where('mapel_id', $mapelId)
            ->where('tingkat', $tingkat)
            ->where('status', 'finished')
            ->where('tahun_ajaran', $this->tahunAktif)
            ->where('semester', $this->smtAktif)
            ->findAll();

        if (empty($jadwalReguler)) {
            return redirect()->back()->with('error', 'Tidak ada jadwal reguler yang berstatus Selesai untuk tingkat ini.');
        }

        $db->transStart();
        $totalSiswaSusulan = 0;
        $ujianService = new \App\Services\UjianService();

        $jurusans = array_unique(array_column($jadwalReguler, 'jurusan'));

        foreach ($jurusans as $jurusan) {
            $jadwalIdsJurusan = [];
            foreach ($jadwalReguler as $jr) {
                if ($jr['jurusan'] === $jurusan) $jadwalIdsJurusan[] = $jr['id'];
            }

            $siswaSusulan = $db->table('siswa')
                ->select('siswa.id as siswa_id')
                ->join('hasil_ujian', "hasil_ujian.siswa_id = siswa.id AND hasil_ujian.jadwal_id IN (" . implode(',', $jadwalIdsJurusan) . ")", 'left')
                ->where('siswa.tingkat', $tingkat)
                ->where('siswa.jurusan', $jurusan)
                ->where('(hasil_ujian.status IN ("pending", "progress") OR hasil_ujian.status IS NULL)')
                ->get()->getResultArray();

            if (!empty($siswaSusulan)) {
                $dataJadwalBaru = [
                    'jenis_ujian_id' => $jenisUjianId,
                    'mapel_id'       => $mapelId,
                    'tingkat'        => $tingkat,
                    'jurusan'        => $jurusan,
                    'ruangan_id'     => $ruanganId,
                    'waktu_mulai'    => $waktuMulai,
                    'waktu_selesai'  => $waktuSelesai,
                    'durasi'         => $durasi > 0 ? $durasi : 90,
                    'status'         => 'ready',
                    'pengawas_id'    => empty($pengawasId) ? null : $pengawasId,
                    'tahun_ajaran'   => $this->tahunAktif,
                    'semester'       => $this->smtAktif
                ];

                $this->jadwalModel->insert($dataJadwalBaru);
                $jadwalBaruId = $this->jadwalModel->getInsertID();

                $ujianService->generateJsonSoal((string)$jadwalBaruId, $mapelId);

                $dataInsertHasil = [];
                $insertedSiswa = [];
                foreach ($siswaSusulan as $siswa) {
                    if (in_array($siswa['siswa_id'], $insertedSiswa)) continue;
                    $insertedSiswa[] = $siswa['siswa_id'];

                    $dataInsertHasil[] = [
                        'jadwal_id'           => $jadwalBaruId,
                        'siswa_id'            => $siswa['siswa_id'],
                        'is_hadir'            => 0,
                        'status'              => 'pending',
                        'waktu_mulai_ujian'   => null,
                        'waktu_selesai_ujian' => null,
                        'jawaban_peserta'     => null,
                        'nilai_pg'            => 0,
                        'nilai_essai'         => 0
                    ];
                    $totalSiswaSusulan++;
                }
                if (!empty($dataInsertHasil)) {
                    $db->table('hasil_ujian')->insertBatch($dataInsertHasil);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem (Rollback). Gagal membuat ujian susulan.');
        }

        if ($totalSiswaSusulan === 0) {
            return redirect()->back()->with('error', 'Tidak ada data siswa yang perlu mengikuti ujian susulan pada mata pelajaran ini.');
        }

        return redirect()->back()->with('success', "Jadwal susulan gabungan berhasil disiapkan untuk $totalSiswaSusulan siswa lintas kelas!");
    }

    private function verifyMapelAccess(string $mapelId): bool
    {
        if (session()->get('role') === 'admin') return true;
        $db = \Config\Database::connect();
        $cek = $db->table('guru_mapel')->where('guru_id', session()->get('id'))->where('mapel_id', $mapelId)->countAllResults();
        return $cek > 0;
    }
}
