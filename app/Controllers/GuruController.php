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
use PhpOffice\PhpSpreadsheet\IOFactory;

class GuruController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function bankSoal()
    {
        $role   = session()->get('role');
        $guruId = session()->get('id');

        if ($role === 'admin') {
            $mapel = $this->db->table('master_mapel')->orderBy('nama_mapel', 'ASC')->get()->getResultArray();
        } else {
            $mapel = $this->db->table('master_mapel')
                ->select('master_mapel.*')
                ->join('guru_mapel', 'guru_mapel.mapel_id = master_mapel.id')
                ->where('guru_mapel.guru_id', $guruId)
                ->orderBy('nama_mapel', 'ASC')
                ->get()->getResultArray();
        }

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

        $filterMapelId = $this->request->getGet('mapel') ?? (!empty($mapel) ? $mapel[0]['id'] : 0);

        $soal = [];
        if ($filterMapelId) {
            $builder = $this->db->table('bank_soal')
                ->select('bank_soal.*, staff.nama_lengkap as nama_guru')
                ->join('staff', 'staff.id = bank_soal.guru_id', 'left')
                ->where('bank_soal.mapel_id', $filterMapelId)
                ->orderBy('bank_soal.id', 'DESC');

            $soal = $builder->get()->getResultArray();
        }

        $data = [
            'title'         => 'Bank Soal - CBT PRO',
            'mapel'         => $mapel,
            'filterMapelId' => $filterMapelId,
            'soal'          => $soal
        ];

        return view('panel/bank_soal', $data);
    }

    public function storeSoal()
    {
        $jenisSoal = $this->request->getPost('jenis_soal');
        $mapelId   = $this->request->getPost('mapel_id');

        $dataInsert = [
            'mapel_id'   => $mapelId,
            'guru_id'    => session()->get('id'),
            'jenis_soal' => $jenisSoal,
            'pertanyaan' => $this->request->getPost('pertanyaan'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $audioFile = $this->request->getFile('file_audio');
        if ($audioFile && $audioFile->isValid() && !$audioFile->hasMoved()) {
            if ($audioFile->getSize() > 2048000) {
                return redirect()->back()->with('error', 'Ukuran file audio maksimal 2MB!');
            }
            $newName = $audioFile->getRandomName();
            $audioFile->move(FCPATH . 'uploads/audio', $newName);
            $dataInsert['file_audio'] = $newName;
        }

        if ($jenisSoal === 'pg') {
            $opsi = [
                'A' => $this->request->getPost('opsi_a'),
                'B' => $this->request->getPost('opsi_b'),
                'C' => $this->request->getPost('opsi_c'),
                'D' => $this->request->getPost('opsi_d'),
                'E' => $this->request->getPost('opsi_e'),
            ];
            $dataInsert['opsi_jawaban']  = json_encode($opsi);
            $dataInsert['kunci_jawaban'] = strtoupper($this->request->getPost('kunci_jawaban'));
        } else {
            $dataInsert['opsi_jawaban']  = null;
            $dataInsert['kunci_jawaban'] = $this->request->getPost('kunci_essai') ?: null;
        }

        $this->db->table('bank_soal')->insert($dataInsert);
        return redirect()->to('/panel/bank-soal?mapel=' . $mapelId)->with('success', 'Soal berhasil disimpan!');
    }

    public function deleteSoal($id)
    {
        $soal = $this->db->table('bank_soal')->where('id', $id)->get()->getRowArray();
        if ($soal && $soal['file_audio']) {
            $path = FCPATH . 'uploads/audio/' . $soal['file_audio'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $this->db->table('bank_soal')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Soal berhasil dihapus.');
    }

    public function createSoal()
    {
        $mapelId = $this->request->getGet('mapel');

        $mapel = $this->db->table('master_mapel')->where('id', $mapelId)->get()->getRowArray();

        if (!$mapel) {
            return redirect()->to('/panel/bank-soal')->with('error', 'Pilih Mata Pelajaran terlebih dahulu!');
        }

        $data = [
            'title'    => 'Input Soal - CBT PRO',
            'mapel_id' => $mapelId,
            'mapel'    => $mapel
        ];

        return view('panel/bank_soal_create', $data);
    }

    public function exportSoal($mapelId)
    {
        $mapel = $this->db->table('master_mapel')->where('id', $mapelId)->get()->getRowArray();
        if (!$mapel) return redirect()->back()->with('error', 'Mata Pelajaran tidak ditemukan.');

        $soal = $this->db->table('bank_soal')->where('mapel_id', $mapelId)->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');

        $headers = ['JENIS (PG/ESSAI)', 'PERTANYAAN', 'OPSI A (Khusus PG)', 'OPSI B (Khusus PG)', 'OPSI C (Khusus PG)', 'OPSI D (Khusus PG)', 'OPSI E (Khusus PG)', 'KUNCI (A/B/C/D/E atau Teks Essai)'];
        $sheet->fromArray($headers, null, 'A1');

        $rowExcel = 2;
        foreach ($soal as $s) {
            $opsi = json_decode($s['opsi_jawaban'], true) ?? ['A' => '', 'B' => '', 'C' => '', 'D' => '', 'E' => ''];
            $pertanyaan = strip_tags($s['pertanyaan']);

            $sheet->setCellValue('A' . $rowExcel, strtoupper($s['jenis_soal']));
            $sheet->setCellValue('B' . $rowExcel, $pertanyaan);
            if ($s['jenis_soal'] == 'pg') {
                $sheet->setCellValue('C' . $rowExcel, strip_tags($opsi['A'] ?? ''));
                $sheet->setCellValue('D' . $rowExcel, strip_tags($opsi['B'] ?? ''));
                $sheet->setCellValue('E' . $rowExcel, strip_tags($opsi['C'] ?? ''));
                $sheet->setCellValue('F' . $rowExcel, strip_tags($opsi['D'] ?? ''));
                $sheet->setCellValue('G' . $rowExcel, strip_tags($opsi['E'] ?? ''));
            }
            $sheet->setCellValue('H' . $rowExcel, strip_tags($s['kunci_jawaban'] ?? ''));
            $rowExcel++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Bank_Soal_' . str_replace(' ', '_', $mapel['nama_mapel']) . '_' . date('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function importSoal()
    {
        $mapelId = $this->request->getPost('mapel_id');
        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'Pilih file Excel yang valid!');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray();

            $dataInsert = [];
            $guruId = session()->get('id');

            foreach ($rows as $key => $row) {
                if ($key == 0) continue;

                $jenis = strtolower(trim($row[0] ?? ''));
                $pertanyaan = trim($row[1] ?? '');

                if (empty($jenis) || empty($pertanyaan)) continue;

                if ($jenis === 'pg') {
                    $opsi = [
                        'A' => trim($row[2] ?? ''),
                        'B' => trim($row[3] ?? ''),
                        'C' => trim($row[4] ?? ''),
                        'D' => trim($row[5] ?? ''),
                        'E' => trim($row[6] ?? ''),
                    ];
                    $dataInsert[] = [
                        'mapel_id'      => $mapelId,
                        'guru_id'       => $guruId,
                        'jenis_soal'    => 'pg',
                        'pertanyaan'    => '<p>' . nl2br($pertanyaan) . '</p>', // Bungkus HTML dasar
                        'opsi_jawaban'  => json_encode($opsi),
                        'kunci_jawaban' => strtoupper(trim($row[7] ?? '')),
                        'created_at'    => date('Y-m-d H:i:s')
                    ];
                } else if ($jenis === 'essai') {
                    $dataInsert[] = [
                        'mapel_id'      => $mapelId,
                        'guru_id'       => $guruId,
                        'jenis_soal'    => 'essai',
                        'pertanyaan'    => '<p>' . nl2br($pertanyaan) . '</p>',
                        'opsi_jawaban'  => null,
                        'kunci_jawaban' => trim($row[7] ?? ''),
                        'created_at'    => date('Y-m-d H:i:s')
                    ];
                }
            }

            if (!empty($dataInsert)) {
                $this->db->table('bank_soal')->insertBatch($dataInsert);
                return redirect()->back()->with('success', 'Berhasil import ' . count($dataInsert) . ' soal!');
            }

            return redirect()->back()->with('error', 'Tidak ada data valid untuk diimport. Pastikan format sesuai.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses Excel: ' . $e->getMessage());
        }
    }

    public function editSoal($id)
    {
        $soal = $this->db->table('bank_soal')->where('id', $id)->get()->getRowArray();

        if (!$soal) {
            return redirect()->to('/panel/bank-soal')->with('error', 'Data soal tidak ditemukan.');
        }

        $mapel = $this->db->table('master_mapel')->where('id', $soal['mapel_id'])->get()->getRowArray();
        $data = [
            'title' => 'Edit Soal - CBT PRO',
            'soal'  => $soal,
            'mapel' => $mapel
        ];

        return view('panel/bank_soal_edit', $data);
    }

    public function updateSoal($id)
    {
        $soalLama = $this->db->table('bank_soal')->where('id', $id)->get()->getRowArray();
        if (!$soalLama) return redirect()->back()->with('error', 'Soal tidak ditemukan.');

        $jenisSoal = $this->request->getPost('jenis_soal');
        $mapelId   = $this->request->getPost('mapel_id');

        $dataUpdate = [
            'jenis_soal' => $jenisSoal,
            'pertanyaan' => $this->request->getPost('pertanyaan'),
        ];

        if ($this->request->getPost('hapus_audio') == '1' && $soalLama['file_audio']) {
            $oldPath = FCPATH . 'uploads/audio/' . $soalLama['file_audio'];
            if (file_exists($oldPath)) unlink($oldPath);
            $dataUpdate['file_audio'] = null;
        }

        $audioFile = $this->request->getFile('file_audio');
        if ($audioFile && $audioFile->isValid() && !$audioFile->hasMoved()) {
            if ($audioFile->getSize() > 2048000) {
                return redirect()->back()->with('error', 'Ukuran file audio maksimal 2MB!');
            }

            if ($soalLama['file_audio']) {
                $oldPath = FCPATH . 'uploads/audio/' . $soalLama['file_audio'];
                if (file_exists($oldPath)) unlink($oldPath);
            }

            $newName = $audioFile->getRandomName();
            $audioFile->move(FCPATH . 'uploads/audio', $newName);
            $dataUpdate['file_audio'] = $newName;
        }

        if ($jenisSoal === 'pg') {
            $opsi = [
                'A' => $this->request->getPost('opsi_a'),
                'B' => $this->request->getPost('opsi_b'),
                'C' => $this->request->getPost('opsi_c'),
                'D' => $this->request->getPost('opsi_d'),
                'E' => $this->request->getPost('opsi_e'),
            ];
            $dataUpdate['opsi_jawaban']  = json_encode($opsi);
            $dataUpdate['kunci_jawaban'] = strtoupper($this->request->getPost('kunci_jawaban'));
        } else {
            $dataUpdate['opsi_jawaban']  = null;
            $dataUpdate['kunci_jawaban'] = $this->request->getPost('kunci_essai') ?: null;
        }

        $this->db->table('bank_soal')->where('id', $id)->update($dataUpdate);
        return redirect()->to('/panel/bank-soal?mapel=' . $mapelId)->with('success', 'Soal berhasil diperbarui!');
    }
}
