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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PenilaianController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $role   = session()->get('role');
        $guruId = session()->get('id');

        $builder = $this->db->table('jadwal_ujian')
            ->select('jadwal_ujian.*, master_mapel.nama_mapel, ruangan.nama_ruangan')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id', 'left')
            ->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id', 'left')
            ->whereIn('jadwal_ujian.status', ['ready', 'active', 'finished']);

        if ($role === 'guru') {
            $mapelGuru = $this->db->table('guru_mapel')->where('guru_id', $guruId)->get()->getResultArray();
            $mapelIds  = array_column($mapelGuru, 'mapel_id');

            if (!empty($mapelIds)) {
                $builder->whereIn('jadwal_ujian.mapel_id', $mapelIds);
            } else {
                $builder->where('1=0');
            }
        }

        $data = [
            'title'  => 'Laporan Nilai Ujian - CBT PRO',
            'jadwal' => $builder->orderBy('jadwal_ujian.waktu_mulai', 'DESC')->get()->getResultArray()
        ];

        return view('panel/penilaian/index', $data);
    }

    public function detail($jadwalId)
    {

        $jadwalRef = $this->db->table('jadwal_ujian')
            ->select('jadwal_ujian.*, master_mapel.nama_mapel, ruangan.nama_ruangan')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id', 'left')
            ->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id', 'left')
            ->where('jadwal_ujian.id', $jadwalId)
            ->get()->getRowArray();

        if (!$jadwalRef) return redirect()->to('/panel/penilaian')->with('error', 'Jadwal tidak ditemukan.');

        $semuaJadwalSerumpun = $this->db->table('jadwal_ujian')
            ->where('mapel_id', $jadwalRef['mapel_id'])
            ->where('tingkat', $jadwalRef['tingkat'])
            ->where('jurusan', $jadwalRef['jurusan'])
            ->get()->getResultArray();

        $arrJadwalIds = array_column($semuaJadwalSerumpun, 'id');
        if (empty($arrJadwalIds)) $arrJadwalIds = [0];

        $siswa = $this->db->table('siswa')
            ->select('siswa.id, siswa.nisn, siswa.nama_lengkap, siswa.tingkat, siswa.jurusan, siswa.rombel, hasil_ujian.nilai_pg, hasil_ujian.nilai_essai, hasil_ujian.status, master_jenis_ujian.nama_ujian as keterangan_ujian, hasil_ujian.jadwal_id as actual_jadwal_id')
            ->join('hasil_ujian', "hasil_ujian.siswa_id = siswa.id AND hasil_ujian.jadwal_id IN (" . implode(',', $arrJadwalIds) . ")", 'left')
            ->join('jadwal_ujian', 'jadwal_ujian.id = hasil_ujian.jadwal_id', 'left')
            ->join('master_jenis_ujian', 'master_jenis_ujian.id = jadwal_ujian.jenis_ujian_id', 'left')
            ->where('siswa.tingkat', $jadwalRef['tingkat'])
            ->where('siswa.jurusan', $jadwalRef['jurusan'])
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title'  => 'Detail Nilai: ' . $jadwalRef['nama_mapel'],
            'jadwal' => $jadwalRef,
            'siswa'  => $siswa
        ];

        return view('panel/penilaian/detail', $data);
    }

    public function koreksi($jadwalId, $siswaId)
    {
        $jadwal = $this->db->table('jadwal_ujian')->where('id', $jadwalId)->get()->getRowArray();
        $siswa  = $this->db->table('siswa')->where('id', $siswaId)->get()->getRowArray();
        $hasil  = $this->db->table('hasil_ujian')
            ->where('jadwal_id', $jadwalId)
            ->where('siswa_id', $siswaId)
            ->get()->getRowArray();

        if (!$hasil || empty($hasil['jawaban_peserta'])) {
            return redirect()->back()->with('error', 'Siswa belum mengerjakan atau belum mengumpulkan ujian.');
        }

        $soalEssai = $this->db->table('bank_soal')
            ->where('mapel_id', $jadwal['mapel_id'])
            ->where('jenis_soal', 'essai')
            ->get()->getResultArray();

        $jawabanSiswa = json_decode($hasil['jawaban_peserta'], true);

        $data = [
            'title'        => 'Koreksi Essai: ' . $siswa['nama_lengkap'],
            'jadwal'       => $jadwal,
            'siswa'        => $siswa,
            'hasil'        => $hasil,
            'soal_essai'   => $soalEssai,
            'jawaban_json' => $jawabanSiswa
        ];

        return view('panel/penilaian/koreksi', $data);
    }

    public function simpanKoreksi()
    {
        $jadwalId   = $this->request->getPost('jadwal_id');
        $siswaId    = $this->request->getPost('siswa_id');
        $nilaiEssai = $this->request->getPost('nilai_essai'); // Input guru 0-100

        $this->db->table('hasil_ujian')
            ->where('jadwal_id', $jadwalId)
            ->where('siswa_id', $siswaId)
            ->update([
                'nilai_essai' => (float)$nilaiEssai
            ]);

        return redirect()->to("/panel/penilaian/detail/$jadwalId")->with('success', 'Nilai Essai berhasil disimpan!');
    }

    public function exportExcel($jadwalId)
    {
        // Ambil jadwal referensi
        $jadwalRef = $this->db->table('jadwal_ujian')
            ->select('jadwal_ujian.*, master_mapel.nama_mapel')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id', 'left')
            ->where('jadwal_ujian.id', $jadwalId)
            ->get()->getRowArray();

        if (!$jadwalRef) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $semuaJadwalSerumpun = $this->db->table('jadwal_ujian')
            ->where('mapel_id', $jadwalRef['mapel_id'])
            ->where('tingkat', $jadwalRef['tingkat'])
            ->where('jurusan', $jadwalRef['jurusan'])
            ->get()->getResultArray();

        $arrJadwalIds = array_column($semuaJadwalSerumpun, 'id');
        if (empty($arrJadwalIds)) $arrJadwalIds = [0];

        // Tarik semua siswa kelas tersebut beserta nilainya
        $siswa = $this->db->table('siswa')
            ->select('siswa.*, hasil_ujian.nilai_pg, hasil_ujian.nilai_essai, hasil_ujian.status, master_jenis_ujian.nama_ujian as keterangan_ujian')
            ->join('hasil_ujian', "hasil_ujian.siswa_id = siswa.id AND hasil_ujian.jadwal_id IN (" . implode(',', $arrJadwalIds) . ")", 'left')
            ->join('jadwal_ujian', 'jadwal_ujian.id = hasil_ujian.jadwal_id', 'left')
            ->join('master_jenis_ujian', 'master_jenis_ujian.id = jadwal_ujian.jenis_ujian_id', 'left')
            ->where('siswa.tingkat', $jadwalRef['tingkat'])
            ->where('siswa.jurusan', $jadwalRef['jurusan'])
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'REKAPITULASI NILAI UJIAN TERPADU');
        $sheet->setCellValue('A2', 'Mata Pelajaran: ' . $jadwalRef['nama_mapel']);
        $sheet->setCellValue('A3', 'Tingkat & Jurusan: ' . $jadwalRef['tingkat'] . ' ' . $jadwalRef['jurusan']);

        $sheet->getStyle('A1:A3')->getFont()->setBold(true);

        $sheet->setCellValue('A5', 'NO');
        $sheet->setCellValue('B5', 'NISN');
        $sheet->setCellValue('C5', 'NAMA LENGKAP');
        $sheet->setCellValue('D5', 'NILAI PG');
        $sheet->setCellValue('E5', 'NILAI ESSAI');
        $sheet->setCellValue('F5', 'TOTAL NILAI (RATA-RATA)');
        $sheet->setCellValue('G5', 'STATUS');
        $sheet->setCellValue('H5', 'KETERANGAN (REGULER/SUSULAN)');

        $sheet->getStyle('A5:H5')->getFont()->setBold(true);
        $sheet->getStyle('A5:H5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');

        $baris = 6;
        $no = 1;
        foreach ($siswa as $s) {
            $pg    = $s['nilai_pg'] ?? 0;
            $essai = $s['nilai_essai'] ?? 0;
            $total = ($pg + $essai) / 2;
            $status = ($s['status'] === 'completed') ? 'SELESAI' : (($s['status'] === 'progress') ? 'MENGERJAKAN' : 'BELUM UJIAN');

            $keterangan = $s['keterangan_ujian'] ?? '-';

            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValueExplicit('B' . $baris, $s['nisn'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $baris, $s['nama_lengkap']);
            $sheet->setCellValue('D' . $baris, $pg);
            $sheet->setCellValue('E' . $baris, $essai);
            $sheet->setCellValue('F' . $baris, $total);
            $sheet->setCellValue('G' . $baris, $status);
            $sheet->setCellValue('H' . $baris, $keterangan);
            $baris++;
        }

        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Nilai_' . str_replace(' ', '_', $jadwalRef['nama_mapel']) . '_' . $jadwalRef['tingkat'] . '_' . $jadwalRef['jurusan'] . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }
}
