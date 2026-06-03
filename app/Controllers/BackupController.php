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

    public function index(): ResponseInterface|string
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
            set_time_limit(0);
            if (function_exists('ini_set')) {
                @ini_set('memory_limit', '1024M');
            }

            $this->backupService->restoreBackup($file);

            return redirect()->back()->with('success', 'Restorasi sistem berhasil dilakukan tanpa kendala!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Restore: ' . $e->getMessage());
        }
    }

    /**
     * ENGINE RESET PABRIK MURNI (Wipe Folder, Migrate Regress, Migrate Up, Seed)
     */
    public function factoryReset(): ResponseInterface
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Akses Ditolak! Hanya Super Admin utama.'
            ]);
        }

        $konfirmasi = $this->request->getPost('konfirmasi_reset');
        if ($konfirmasi !== 'HAPUS PERMANEN') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Validasi gagal! Kata sandi ketikan tidak sesuai.'
            ]);
        }

        try {
            // =======================================================
            // TAHAP 1: MEMBABAT HABIS FILE MEDIA DI FOLDER
            // =======================================================
            $folderDibersihkan = [
                FCPATH . 'uploads/',         // Sapu logo lama
                FCPATH . 'uploads/soal/',    // Sapu gambar soal
                FCPATH . 'uploads/audio/',   // Sapu audio listening
                FCPATH . 'data_soal/',       // Sapu file JSON jadwal/soal
                FCPATH . 'data_ruangan/',    // Sapu cache ruangan
                WRITEPATH . 'uploads/'       // Sapu temp file CI4
            ];

            // Melindungi file pondasi agar folder tidak terekspos / error
            $skipFiles = ['.', '..', 'index.html', '.htaccess'];

            foreach ($folderDibersihkan as $folder) {
                if (is_dir($folder)) {
                    $files = scandir($folder);
                    foreach ($files as $file) {
                        if (!in_array($file, $skipFiles)) {
                            $filePath = $folder . $file;
                            if (is_file($filePath)) {
                                @unlink($filePath);
                            }
                        }
                    }
                }
            }

            // =======================================================
            // TAHAP 2: BONGKAR & BANGUN ULANG DATABASE
            // =======================================================
            $db = \Config\Database::connect();

            // Matikan Foreign Key Checks agar Regress (Drop Table) berjalan lancar tanpa error relasi
            $db->query('SET FOREIGN_KEY_CHECKS = 0');

            // 1. Memanggil layanan Migration bawaan CI4
            $migrate = \Config\Services::migrations();
            $migrate->setSilent(true);

            // 2. Rollback Total (Menjalankan fungsi down() di CbtSystem.php untuk DROP semua tabel)
            $migrate->regress(0);

            // 3. Bangun Ulang (Menjalankan fungsi up() di CbtSystem.php untuk CREATE tabel fresh)
            $migrate->latest();

            // 4. Suntikkan Data Pabrik (Menjalankan ProdSeeder.php)
            $seeder = \Config\Database::seeder();
            $seeder->call('ProdSeeder');

            // Nyalakan kembali penjaga integritas relasi Database
            $db->query('SET FOREIGN_KEY_CHECKS = 1');

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Sistem berhasil direset ke standar awal pabrik.'
            ]);
        } catch (\Throwable $e) {
            // Tangkap dan kembalikan pesan jika terjadi error server tingkat rendah
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem saat me-reset: ' . $e->getMessage()
            ]);
        }
    }
}
