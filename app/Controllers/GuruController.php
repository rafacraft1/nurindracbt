<?php

namespace App\Controllers;

use App\Models\BankSoalModel;
use App\Models\MapelModel;
use App\Services\ExcelService;
use CodeIgniter\HTTP\ResponseInterface;

class GuruController extends BaseController
{
    protected BankSoalModel $bankSoalModel;
    protected MapelModel $mapelModel;
    protected ExcelService $excelService;

    public function __construct()
    {
        $this->bankSoalModel = new BankSoalModel();
        $this->mapelModel    = new MapelModel();
        $this->excelService  = new ExcelService();
    }

    public function index(): string
    {
        $role   = (string)session()->get('role');
        $guruId = (string)session()->get('id');
        $db     = \Config\Database::connect();

        if ($role === 'admin') {
            $mapel = $this->mapelModel->orderBy('nama_mapel', 'ASC')->findAll();
        } else {
            $mapel = $this->mapelModel->select('master_mapel.*')
                ->join('guru_mapel', 'guru_mapel.mapel_id = master_mapel.id')
                ->where('guru_mapel.guru_id', $guruId)
                ->orderBy('nama_mapel', 'ASC')
                ->findAll();
        }

        foreach ($mapel as &$m) {
            $m['total_pg']    = $this->bankSoalModel->countSoalByJenis((string)$m['id'], 'pg');
            $m['total_essai'] = $this->bankSoalModel->countSoalByJenis((string)$m['id'], 'essai');
        }

        $filterMapelId = (string)($this->request->getGet('mapel') ?? (!empty($mapel) ? $mapel[0]['id'] : '0'));
        $soal = $filterMapelId !== '0' ? $this->bankSoalModel->getSoalByMapel($filterMapelId) : [];

        $data = [
            'title'         => 'Bank Soal - CBT PRO',
            'mapel'         => $mapel,
            'filterMapelId' => $filterMapelId,
            'soal'          => $soal
        ];

        return view('panel/bank_soal', $data);
    }

    public function create(): ResponseInterface|string
    {
        $mapelId = (string)$this->request->getGet('mapel');
        $mapel   = $this->mapelModel->find($mapelId);

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

    public function store(): ResponseInterface
    {
        $jenisSoal = (string)$this->request->getPost('jenis_soal');
        $mapelId   = (string)$this->request->getPost('mapel_id');

        $dataInsert = [
            'mapel_id'   => $mapelId,
            'guru_id'    => (string)session()->get('id'),
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
            $dataInsert['kunci_jawaban'] = strtoupper((string)$this->request->getPost('kunci_jawaban'));
        } else {
            $dataInsert['opsi_jawaban']  = null;
            $dataInsert['kunci_jawaban'] = (string)$this->request->getPost('kunci_essai') ?: null;
        }

        $this->bankSoalModel->insert($dataInsert);
        return redirect()->to('/panel/bank-soal?mapel=' . $mapelId)->with('success', 'Soal berhasil disimpan!');
    }

    public function edit(string $id): ResponseInterface|string
    {
        $soal = $this->bankSoalModel->find($id);

        if (!$soal) {
            return redirect()->to('/panel/bank-soal')->with('error', 'Data soal tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Soal - CBT PRO',
            'soal'  => $soal,
            'mapel' => $this->mapelModel->find($soal['mapel_id'])
        ];

        return view('panel/bank_soal_edit', $data);
    }

    public function update(string $id): ResponseInterface
    {
        $soalLama = $this->bankSoalModel->find($id);
        if (!$soalLama) return redirect()->back()->with('error', 'Soal tidak ditemukan.');

        $jenisSoal = (string)$this->request->getPost('jenis_soal');
        $mapelId   = (string)$this->request->getPost('mapel_id');

        $dataUpdate = [
            'jenis_soal' => $jenisSoal,
            'pertanyaan' => $this->request->getPost('pertanyaan'),
        ];

        if ($this->request->getPost('hapus_audio') === '1' && !empty($soalLama['file_audio'])) {
            $oldPath = FCPATH . 'uploads/audio/' . $soalLama['file_audio'];
            if (file_exists($oldPath)) unlink($oldPath);
            $dataUpdate['file_audio'] = null;
        }

        $audioFile = $this->request->getFile('file_audio');
        if ($audioFile && $audioFile->isValid() && !$audioFile->hasMoved()) {
            if ($audioFile->getSize() > 2048000) {
                return redirect()->back()->with('error', 'Ukuran file audio maksimal 2MB!');
            }
            if (!empty($soalLama['file_audio'])) {
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
            $dataUpdate['kunci_jawaban'] = strtoupper((string)$this->request->getPost('kunci_jawaban'));
        } else {
            $dataUpdate['opsi_jawaban']  = null;
            $dataUpdate['kunci_jawaban'] = (string)$this->request->getPost('kunci_essai') ?: null;
        }

        $this->bankSoalModel->update($id, $dataUpdate);
        return redirect()->to('/panel/bank-soal?mapel=' . $mapelId)->with('success', 'Soal berhasil diperbarui!');
    }

    public function delete(string $id): ResponseInterface
    {
        $soal = $this->bankSoalModel->find($id);
        if ($soal && !empty($soal['file_audio'])) {
            $path = FCPATH . 'uploads/audio/' . $soal['file_audio'];
            if (file_exists($path)) unlink($path);
        }

        $this->bankSoalModel->delete($id);
        return redirect()->back()->with('success', 'Soal berhasil dihapus.');
    }

    public function export(string $mapelId)
    {
        $mapel = $this->mapelModel->find($mapelId);
        if (!$mapel) return redirect()->back()->with('error', 'Mata Pelajaran tidak ditemukan.');

        $soal = $this->bankSoalModel->where('mapel_id', $mapelId)->findAll();

        $spreadsheet = $this->excelService->buildBankSoalExcel($soal);
        $writer      = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename    = 'Bank_Soal_' . str_replace(' ', '_', $mapel['nama_mapel']) . '_' . date('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function import(): ResponseInterface
    {
        $mapelId = (string)$this->request->getPost('mapel_id');
        $file    = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'Pilih file Excel yang valid!');
        }

        try {
            $guruId = (string)session()->get('id');
            $dataInsert = $this->excelService->parseBankSoalExcel($file, $mapelId, $guruId);

            if (!empty($dataInsert)) {
                $this->bankSoalModel->insertBatch($dataInsert);
                return redirect()->back()->with('success', 'Berhasil import ' . count($dataInsert) . ' soal!');
            }

            return redirect()->back()->with('error', 'Tidak ada data valid untuk diimport. Pastikan format sesuai.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses Excel: ' . $e->getMessage());
        }
    }

    public function uploadGambar(): ResponseInterface
    {
        if ($this->request->isAJAX()) {
            $file = $this->request->getFile('gambar_soal');

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/soal/', $newName);

                return $this->response->setJSON([
                    'success' => true,
                    'url'     => base_url('uploads/soal/' . $newName),
                    'csrf'    => csrf_hash()
                ]);
            }

            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses file gambar.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request.']);
    }
}
