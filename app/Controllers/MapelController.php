<?php

namespace App\Controllers;

use App\Models\MapelModel;
use CodeIgniter\HTTP\ResponseInterface;

class MapelController extends BaseController
{
    protected MapelModel $mapelModel;

    public function __construct()
    {
        $this->mapelModel = new MapelModel();
    }

    public function index(): string
    {
        $db = \Config\Database::connect();

        $mapel = $this->mapelModel->orderBy('nama_mapel', 'ASC')->findAll();
        $semua_guru = $db->table('staff')->where('role', 'guru')->orderBy('nama_lengkap', 'ASC')->get()->getResultArray();

        // Menyusun statistik (Bisa dipindah ke model jika querynya makin kompleks di masa depan)
        foreach ($mapel as &$m) {
            $m['guru_pengampu'] = $db->table('guru_mapel')
                ->select('staff.id, staff.nama_lengkap')
                ->join('staff', 'staff.id = guru_mapel.guru_id')
                ->where('mapel_id', $m['id'])
                ->get()->getResultArray();

            $m['total_pg'] = $db->table('bank_soal')
                ->where('mapel_id', $m['id'])
                ->where('jenis_soal', 'pg')
                ->countAllResults();

            $m['total_essai'] = $db->table('bank_soal')
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

    public function store(): ResponseInterface
    {
        $namaMapel = strtoupper((string)$this->request->getPost('nama_mapel'));

        if ($this->mapelModel->where('nama_mapel', $namaMapel)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Mata Pelajaran sudah ada!');
        }

        $this->mapelModel->insert(['nama_mapel' => $namaMapel]);
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function update(string $id): ResponseInterface
    {
        $namaMapel = strtoupper((string)$this->request->getPost('nama_mapel'));

        if ($this->mapelModel->where('nama_mapel', $namaMapel)->where('id !=', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Gagal! Nama Mata Pelajaran sudah dipakai.');
        }

        $this->mapelModel->update($id, ['nama_mapel' => $namaMapel]);
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function delete(string $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $db->table('guru_mapel')->where('mapel_id', $id)->delete();

        $this->mapelModel->delete($id);
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil dihapus.');
    }

    public function syncGuru(): ResponseInterface
    {
        $mapel_id = (string)$this->request->getPost('mapel_id');
        $guru_ids = $this->request->getPost('guru_ids'); // berupa array

        $db = \Config\Database::connect();

        $db->transStart();
        $db->table('guru_mapel')->where('mapel_id', $mapel_id)->delete();

        if (!empty($guru_ids) && is_array($guru_ids)) {
            $dataInsert = [];
            foreach ($guru_ids as $g_id) {
                $dataInsert[] = ['guru_id' => $g_id, 'mapel_id' => $mapel_id];
            }
            $db->table('guru_mapel')->insertBatch($dataInsert);
        }
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memperbarui relasi Guru Pengampu.');
        }

        return redirect()->back()->with('success', 'Relasi Guru Pengampu berhasil diperbarui.');
    }
}
