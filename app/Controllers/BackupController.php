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
                FCPATH . 'uploads/',
                FCPATH . 'data_soal/',
                FCPATH . 'data_ruangan/',
                WRITEPATH . 'uploads/'
            ];

            foreach ($folderDibersihkan as $folder) {
                if (is_dir($folder)) {
                    $this->recursiveEmptyDir($folder);
                }
            }

            // =======================================================
            // TAHAP 2: BONGKAR & BANGUN ULANG DATABASE
            // =======================================================
            $db = \Config\Database::connect();
            $db->query('SET FOREIGN_KEY_CHECKS = 0');

            $migrate = \Config\Services::migrations();
            $migrate->setSilent(true);
            $migrate->regress(0);
            $migrate->latest();

            $seeder = \Config\Database::seeder();
            $seeder->call('ProdSeeder');

            $db->query('SET FOREIGN_KEY_CHECKS = 1');

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Sistem berhasil direset ke standar awal pabrik.'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem saat me-reset: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * FIX LOGIKA: Helper khusus untuk menghapus isi folder secara rekursif hingga ke akar
     * namun tetap menjaga struktur pondasi folder dan file index.html/.htaccess
     */
    private function recursiveEmptyDir(string $dir): void
    {
        $skipFiles = ['.', '..', 'index.html', '.htaccess'];
        $items = scandir($dir);

        foreach ($items as $item) {
            if (in_array($item, $skipFiles)) {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                $this->recursiveEmptyDir($path);
                @rmdir($path); // Hapus folder kosong setelah isinya dibersihkan
            } else {
                @unlink($path);
            }
        }
    }
}
