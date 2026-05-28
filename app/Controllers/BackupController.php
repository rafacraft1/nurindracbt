<?php

namespace App\Controllers;

use App\Services\BackupService;
use CodeIgniter\HTTP\ResponseInterface;

class BackupController extends BaseController
{
    protected BackupService $backupService;

    public function __construct()
    {
        $this->backupService = new BackupService();
    }

    private function checkAdmin(): void
    {
        if (session()->get('role') !== 'admin') {
            header('Location: /panel/dashboard');
            exit;
        }
    }

    public function index(): string
    {
        $this->checkAdmin();
        $data = [
            'title' => 'Backup & Restore - CBT PRO'
        ];
        return view('panel/backup_restore', $data);
    }

    public function download(string $type): ResponseInterface
    {
        $this->checkAdmin();

        try {
            $filePath = $this->backupService->generateBackup($type);
            return $this->response->download($filePath, null)->setFileName(basename($filePath));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function restore(): ResponseInterface
    {
        $this->checkAdmin();

        $file = $this->request->getFile('file_backup');

        if (!$file || !$file->isValid() || $file->hasMoved() || $file->getClientExtension() !== 'zip') {
            return redirect()->back()->with('error', 'Gagal! Pilih file backup (.zip) yang valid.');
        }

        try {
            // Proses berpotensi memakan waktu, berikan waktu tambahan ke server
            set_time_limit(0);
            ini_set('memory_limit', '512M');

            $this->backupService->restoreBackup($file);

            return redirect()->back()->with('success', 'Restorasi sistem berhasil dilakukan tanpa kendala!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Restore: ' . $e->getMessage());
        }
    }

    public function factoryReset(): \CodeIgniter\HTTP\ResponseInterface
    {
        // Pastikan hanya admin utama yang bisa melakukan ini
        if (session()->get('username') !== 'admin') {
            return redirect()->back()->with('error', 'Akses Ditolak! Hanya Super Admin utama yang dapat melakukan Factory Reset.');
        }

        $db = \Config\Database::connect();

        $db->transStart();
        $db->query('SET FOREIGN_KEY_CHECKS = 0');

        // 1. Kosongkan tabel transaksional & master
        $tabelDikosongkan = [
            'siswa',
            'hasil_ujian',
            'bank_soal',
            'jadwal_ujian',
            'ruangan',
            'master_mapel',
            'master_jenis_ujian',
            'guru_mapel'
        ];

        foreach ($tabelDikosongkan as $tabel) {
            $db->table($tabel)->truncate();
        }

        // 2. Hapus semua staff KECUALI admin utama
        $db->table('staff')->where('username !=', 'admin')->delete();

        $db->query('SET FOREIGN_KEY_CHECKS = 1');
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal mereset database. Proses dibatalkan otomatis.');
        }

        // 3. Bersihkan Folder File Dinamis
        $folderDibersihkan = [
            FCPATH . 'uploads/soal/',
            FCPATH . 'uploads/audio/',
            FCPATH . 'data_soal/',
            FCPATH . 'data_ruangan/',
            WRITEPATH . 'uploads/'
        ];

        foreach ($folderDibersihkan as $folder) {
            if (is_dir($folder)) {
                $files = array_diff(scandir($folder), ['.', '..', 'index.html']);
                foreach ($files as $file) {
                    $filePath = $folder . $file;
                    if (is_file($filePath)) {
                        unlink($filePath);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Sistem berhasil dikembalikan ke Setelan Pabrik. Semua data bersih!');
    }
}
