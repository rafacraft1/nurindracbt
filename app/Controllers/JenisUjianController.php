<?php

namespace App\Controllers;

use App\Models\JenisUjianModel;
use CodeIgniter\HTTP\ResponseInterface;

class JenisUjianController extends BaseController
{
    protected JenisUjianModel $jenisUjianModel;

    public function __construct()
    {
        $this->jenisUjianModel = new JenisUjianModel();
    }

    public function index(): string
    {
        $data = [
            'title'       => 'Master Jenis Ujian - CBT PRO',
            'jenis_ujian' => $this->jenisUjianModel->orderBy('id', 'DESC')->findAll()
        ];
        return view('panel/jenis_ujian', $data);
    }

    public function store(): ResponseInterface
    {
        $namaUjian = strtoupper((string)$this->request->getPost('nama_ujian'));
        if ($this->jenisUjianModel->where('nama_ujian', $namaUjian)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Jenis Ujian tersebut sudah ada!');
        }
        $this->jenisUjianModel->insert(['nama_ujian' => $namaUjian]);
        return redirect()->back()->with('success', 'Jenis Ujian berhasil ditambahkan.');
    }

    public function update(string $id): ResponseInterface
    {
        $namaUjian = strtoupper((string)$this->request->getPost('nama_ujian'));
        if ($this->jenisUjianModel->where('nama_ujian', $namaUjian)->where('id !=', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Gagal! Nama Jenis Ujian sudah dipakai.');
        }
        $this->jenisUjianModel->update($id, ['nama_ujian' => $namaUjian]);
        return redirect()->back()->with('success', 'Jenis Ujian berhasil diperbarui.');
    }

    public function delete(string $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        if ($db->table('jadwal_ujian')->where('jenis_ujian_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Gagal Menghapus! Jenis Ujian ini sedang digunakan pada Jadwal Ujian.');
        }
        $this->jenisUjianModel->delete($id);
        return redirect()->back()->with('success', 'Jenis Ujian berhasil dihapus.');
    }
}
