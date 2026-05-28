<?php

namespace App\Services;

// PERBAIKAN: Menggunakan namespace HTTP Files yang benar
use CodeIgniter\HTTP\Files\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;

class ExcelService
{
    /**
     * Membaca dan membersihkan baris Excel dari UploadedFile
     */
    public function parseSiswaExcel(UploadedFile $file): array
    {
        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray();

            $cleanRows = [];
            foreach ($rows as $key => $row) {
                if ($key == 0) continue; // Abaikan Header (Baris 1)

                // Casting ke string untuk mencegah error Intelephense P1132
                $nisn = trim((string)($row[0] ?? ''));
                $nama = trim((string)($row[1] ?? ''));

                if (empty($nisn) || empty($nama)) continue;

                $cleanRows[] = $row;
            }

            return $cleanRows;
        } catch (Exception $e) {
            throw new Exception('Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Membuat object Spreadsheet untuk Export Bank Soal
     */
    public function buildBankSoalExcel(array $soal): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');

        $headers = ['JENIS (PG/ESSAI)', 'PERTANYAAN', 'OPSI A (Khusus PG)', 'OPSI B (Khusus PG)', 'OPSI C (Khusus PG)', 'OPSI D (Khusus PG)', 'OPSI E (Khusus PG)', 'KUNCI (A/B/C/D/E atau Teks Essai)'];
        $sheet->fromArray($headers, null, 'A1');

        $rowExcel = 2;
        foreach ($soal as $s) {
            $opsi = json_decode((string)$s['opsi_jawaban'], true) ?? ['A' => '', 'B' => '', 'C' => '', 'D' => '', 'E' => ''];
            $pertanyaan = strip_tags((string)$s['pertanyaan']);

            $sheet->setCellValue('A' . $rowExcel, strtoupper((string)$s['jenis_soal']));
            $sheet->setCellValue('B' . $rowExcel, $pertanyaan);
            if ($s['jenis_soal'] === 'pg') {
                $sheet->setCellValue('C' . $rowExcel, strip_tags($opsi['A'] ?? ''));
                $sheet->setCellValue('D' . $rowExcel, strip_tags($opsi['B'] ?? ''));
                $sheet->setCellValue('E' . $rowExcel, strip_tags($opsi['C'] ?? ''));
                $sheet->setCellValue('F' . $rowExcel, strip_tags($opsi['D'] ?? ''));
                $sheet->setCellValue('G' . $rowExcel, strip_tags($opsi['E'] ?? ''));
            }
            $sheet->setCellValue('H' . $rowExcel, strip_tags((string)($s['kunci_jawaban'] ?? '')));
            $rowExcel++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Membaca dan mem-parsing Excel Import Bank Soal
     */
    public function parseBankSoalExcel(UploadedFile $file, string $mapelId, string $guruId): array
    {
        $spreadsheet = IOFactory::load($file->getTempName());
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray();

        $dataInsert = [];

        foreach ($rows as $key => $row) {
            if ($key == 0) continue;

            $jenis = strtolower(trim((string)($row[0] ?? '')));
            $pertanyaan = trim((string)($row[1] ?? ''));

            if (empty($jenis) || empty($pertanyaan)) continue;

            if ($jenis === 'pg') {
                $opsi = [
                    'A' => trim((string)($row[2] ?? '')),
                    'B' => trim((string)($row[3] ?? '')),
                    'C' => trim((string)($row[4] ?? '')),
                    'D' => trim((string)($row[5] ?? '')),
                    'E' => trim((string)($row[6] ?? '')),
                ];
                $dataInsert[] = [
                    'mapel_id'      => $mapelId,
                    'guru_id'       => $guruId,
                    'jenis_soal'    => 'pg',
                    'pertanyaan'    => '<p>' . nl2br($pertanyaan) . '</p>',
                    'opsi_jawaban'  => json_encode($opsi),
                    'kunci_jawaban' => strtoupper(trim((string)($row[7] ?? ''))),
                    'created_at'    => date('Y-m-d H:i:s')
                ];
            } else if ($jenis === 'essai') {
                $dataInsert[] = [
                    'mapel_id'      => $mapelId,
                    'guru_id'       => $guruId,
                    'jenis_soal'    => 'essai',
                    'pertanyaan'    => '<p>' . nl2br($pertanyaan) . '</p>',
                    'opsi_jawaban'  => null,
                    'kunci_jawaban' => trim((string)($row[7] ?? '')),
                    'created_at'    => date('Y-m-d H:i:s')
                ];
            }
        }

        return $dataInsert;
    }
    /**
     * Membuat object Spreadsheet untuk Export Rekap Nilai Ujian
     */
    public function buildRekapNilaiExcel(array $jadwalRef, array $siswa): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'REKAPITULASI NILAI UJIAN TERPADU');
        $sheet->setCellValue('A2', 'Mata Pelajaran: ' . $jadwalRef['nama_mapel']);
        $sheet->setCellValue('A3', 'Tingkat & Jurusan: ' . $jadwalRef['tingkat'] . ' ' . $jadwalRef['jurusan']);
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);

        $headers = ['NO', 'NISN', 'NAMA LENGKAP', 'NILAI PG', 'NILAI ESSAI', 'TOTAL NILAI (RATA-RATA)', 'STATUS', 'KETERANGAN (REGULER/SUSULAN)'];
        $sheet->fromArray($headers, null, 'A5');
        $sheet->getStyle('A5:H5')->getFont()->setBold(true);
        $sheet->getStyle('A5:H5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');

        $baris = 6;
        $no = 1;

        foreach ($siswa as $s) {
            $pg    = (float)($s['nilai_pg'] ?? 0);
            $essai = (float)($s['nilai_essai'] ?? 0);
            $total = ($pg + $essai) / 2;
            $status = ($s['status'] === 'completed') ? 'SELESAI' : (($s['status'] === 'progress') ? 'MENGERJAKAN' : 'BELUM UJIAN');
            $keterangan = (string)($s['keterangan_ujian'] ?? '-');

            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValueExplicit('B' . $baris, (string)$s['nisn'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $baris, (string)$s['nama_lengkap']);
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

        return $spreadsheet;
    }
}
