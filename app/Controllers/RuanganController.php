<?php

namespace App\Controllers;

use App\Models\RuanganModel;
use App\Models\SiswaModel;
use App\Models\JadwalModel; // Tambahkan ini untuk proteksi ujian
use CodeIgniter\HTTP\ResponseInterface;

class RuanganController extends BaseController
{
    protected RuanganModel $ruanganModel;
    protected SiswaModel $siswaModel;
    protected JadwalModel $jadwalModel;

    public function __construct()
    {
        $this->ruanganModel = new RuanganModel();
        $this->siswaModel   = new SiswaModel();
        $this->jadwalModel  = new JadwalModel();
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

        if (empty($namaRuangan)) {
            return redirect()->back()->with('error', 'Nama ruangan tidak boleh kosong!');
        }

        if ($this->ruanganModel->where('nama_ruangan', $namaRuangan)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Nama Ruangan sudah ada!');
        }

        $this->ruanganModel->insert(['nama_ruangan' => $namaRuangan]);
        return redirect()->back()->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function update(string $id): ResponseInterface
    {
        $namaRuangan = strtoupper((string)$this->request->getPost('nama_ruangan'));

        if (empty($namaRuangan)) {
            return redirect()->back()->with('error', 'Nama ruangan tidak boleh kosong!');
        }

        $cekDuplikat = $this->ruanganModel
            ->where('nama_ruangan', $namaRuangan)
            ->where('id !=', $id)
            ->countAllResults();

        if ($cekDuplikat > 0) {
            return redirect()->back()->with('error', 'Gagal: Nama Ruangan tersebut sudah ada!');
        }

        $this->ruanganModel->update($id, ['nama_ruangan' => $namaRuangan]);
        return redirect()->back()->with('success', 'Nama ruangan berhasil diperbarui.');
    }

    public function delete(string $id): ResponseInterface
    {
        // 1. Cek apakah ruangan valid
        $ruangan = $this->ruanganModel->find($id);
        if (!$ruangan) return redirect()->back()->with('error', 'Ruangan tidak ditemukan.');

        // 2. Proteksi Logika Bisnis: Cegah hapus jika ada ujian aktif
        $ujianAktif = $this->jadwalModel->where('status', 'active')->countAllResults();
        if ($ujianAktif > 0) {
            return redirect()->back()->with('error', 'Gagal menghapus! Sistem mendeteksi ada ujian yang sedang berlangsung.');
        }

        $db = \Config\Database::connect();
        $db->transStart(); // Mulai Transaksi Anti-Corrupt

        try {
            $this->siswaModel->kosongkanRuangan($id);
            $this->ruanganModel->delete($id);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menghapus ruangan.');
            }

            return redirect()->back()->with('success', 'Ruangan berhasil dihapus permanen.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menghapus ruangan: ' . $e->getMessage());
        }
    }

    public function plotSiswa(): ResponseInterface
    {
        $ruangan_id = (string)$this->request->getPost('ruangan_id');
        $siswa_ids  = $this->request->getPost('siswa_ids');

        // Validasi keberadaan ruangan di database (Mencegah Injeksi ID Fiktif)
        if (!$this->ruanganModel->find($ruangan_id)) {
            return redirect()->back()->with('error', 'Manipulasi data terdeteksi: ID Ruangan tidak valid!');
        }

        $db = \Config\Database::connect();
        $db->transStart(); // Mulai Transaksi (Sapu bersih & Isi ulang dalam 1 nafas)

        try {
            $this->siswaModel->kosongkanRuangan($ruangan_id);

            if (!empty($siswa_ids) && is_array($siswa_ids)) {
                $this->siswaModel->whereIn('id', $siswa_ids)
                    ->set(['ruangan_id' => $ruangan_id])
                    ->update();
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal memproses plot ruangan karena masalah database.');
            }

            return redirect()->back()->with('success', 'Data penghuni ruangan berhasil disinkronisasi.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function kosongkan(string $id): ResponseInterface
    {
        // Proteksi Logika Bisnis: Cegah kosongkan jika ada ujian aktif
        $ujianAktif = $this->jadwalModel->where('status', 'active')->countAllResults();
        if ($ujianAktif > 0) {
            return redirect()->back()->with('error', 'Gagal! Tidak dapat mengosongkan ruangan saat ujian sedang berlangsung.');
        }

        $this->siswaModel->kosongkanRuangan($id);
        return redirect()->back()->with('success', 'Ruangan berhasil dikosongkan.');
    }
}
