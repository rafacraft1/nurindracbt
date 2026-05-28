<?php

namespace App\Controllers;

use App\Models\RuanganModel;
use App\Models\SiswaModel;
use CodeIgniter\HTTP\ResponseInterface;

class RuanganController extends BaseController
{
    protected RuanganModel $ruanganModel;
    protected SiswaModel $siswaModel;

    public function __construct()
    {
        $this->ruanganModel = new RuanganModel();
        $this->siswaModel   = new SiswaModel();
    }

    public function index(): string
    {
        $data = [
            'title'   => 'Manajemen Ruangan - CBT PRO',
            'ruangan' => $this->ruanganModel->getRuanganDenganJumlahSiswa(),
            'siswa'   => $this->siswaModel->select('id, nisn, nama_lengkap, tingkat, jurusan, rombel, ruangan_id')
                ->orderBy('tingkat', 'ASC')
                ->orderBy('jurusan', 'ASC')
                ->orderBy('rombel', 'ASC')
                ->orderBy('nama_lengkap', 'ASC')
                ->findAll()
        ];

        return view('panel/ruangan', $data);
    }

    public function store(): ResponseInterface
    {
        $namaRuangan = strtoupper((string)$this->request->getPost('nama_ruangan'));

        if ($this->ruanganModel->where('nama_ruangan', $namaRuangan)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Nama Ruangan sudah ada!');
        }

        $this->ruanganModel->insert(['nama_ruangan' => $namaRuangan]);
        return redirect()->back()->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function delete(string $id): ResponseInterface
    {
        // Kosongkan relasi siswa ke ruangan ini sebelum dihapus
        $this->siswaModel->kosongkanRuangan($id);

        $this->ruanganModel->delete($id);
        return redirect()->back()->with('success', 'Ruangan berhasil dihapus.');
    }

    public function plotSiswa(): ResponseInterface
    {
        $ruangan_id = (string)$this->request->getPost('ruangan_id');
        $siswa_ids  = $this->request->getPost('siswa_ids'); // berupa array

        // Kosongkan dulu siswa lama di ruangan ini
        $this->siswaModel->kosongkanRuangan($ruangan_id);

        if (!empty($siswa_ids) && is_array($siswa_ids)) {
            $this->siswaModel->whereIn('id', $siswa_ids)->set(['ruangan_id' => $ruangan_id])->update();
        }

        return redirect()->back()->with('success', 'Data penghuni ruangan berhasil disinkronisasi.');
    }

    public function kosongkan(string $id): ResponseInterface
    {
        $this->siswaModel->kosongkanRuangan($id);
        return redirect()->back()->with('success', 'Ruangan berhasil dikosongkan.');
    }
}
