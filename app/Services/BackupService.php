<?php

namespace App\Services;

use CodeIgniter\HTTP\Files\UploadedFile;
use Exception;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class BackupService
{
    public function generateBackup(string $tipe): string
    {
        $db  = \Config\Database::connect();
        $zip = new ZipArchive();

        $filename = 'Backup_CBT_' . strtoupper($tipe) . '_' . date('Ymd_His') . '.zip';
        $filepath = WRITEPATH . 'uploads/' . $filename;

        if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception('Gagal membuat file ZIP Backup.');
        }

        $tables  = [];
        $folders = [];

        switch ($tipe) {
            case 'staff':
                $tables  = ['staff', 'pengaturan'];
                $folders = ['uploads'];
                break;
            case 'siswa':
                $tables  = ['siswa', 'hasil_ujian'];
                break;
            case 'soal':
                $tables  = ['bank_soal', 'master_mapel', 'master_jenis_ujian'];
                $folders = ['uploads/soal', 'uploads/audio', 'data_soal'];
                break;
            case 'full':
                $tables  = $db->listTables();
                $folders = ['uploads', 'data_soal', 'data_ruangan'];
                break;
            default:
                throw new Exception('Tipe backup tidak valid.');
        }

        $dataDb = [];
        foreach ($tables as $table) {
            // FIX: Menggunakan builder yang lebih ringan agar tidak memicu memory_limit jika data besar
            $dataDb[$table] = $db->table($table)->get()->getResultArray();
        }

        $zip->addFromString('database.json', json_encode([
            'tipe'  => $tipe,
            'waktu' => date('Y-m-d H:i:s'),
            'data'  => $dataDb
        ]));

        foreach ($folders as $folder) {
            $dirPath = FCPATH . $folder;
            if (is_dir($dirPath)) {
                $this->addDirToZip($dirPath, $zip, $folder);
            }
        }

        $zip->close();
        return $filepath;
    }

    public function restoreBackup(UploadedFile $file): bool
    {
        $zip = new ZipArchive();
        if ($zip->open($file->getTempName()) !== true) {
            throw new Exception('File ZIP tidak dapat dibuka atau corrupt.');
        }

        $jsonContent = $zip->getFromName('database.json');
        if (!$jsonContent) {
            $zip->close();
            throw new Exception('File backup tidak valid. (database.json tidak ditemukan).');
        }

        $backupData = json_decode($jsonContent, true);
        if (!isset($backupData['data'])) {
            $zip->close();
            throw new Exception('Struktur file JSON rusak.');
        }

        $db = \Config\Database::connect();

        $db->transStart();
        $db->query('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($backupData['data'] as $table => $rows) {
            if ($db->tableExists($table)) {
                $db->table($table)->truncate();
                if (!empty($rows)) {
                    $chunks = array_chunk($rows, 100);
                    foreach ($chunks as $chunk) {
                        $db->table($table)->insertBatch($chunk);
                    }
                }
            }
        }

        $db->query('SET FOREIGN_KEY_CHECKS = 1');
        $db->transComplete();

        if ($db->transStatus() === false) {
            $zip->close();
            throw new Exception('Gagal merestore database. Proses dibatalkan secara otomatis (Rollback).');
        }

        $allowedDirs = ['uploads/', 'data_soal/', 'data_ruangan/'];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);

            // FIX SECURITY: Mencegah serangan Zip Slip (Path Traversal Vulnerability)
            if (strpos($filename, '../') !== false || strpos($filename, '..\\') !== false) {
                continue;
            }

            $isAllowed = false;
            foreach ($allowedDirs as $dir) {
                if (strpos($filename, $dir) === 0) {
                    $isAllowed = true;
                    break;
                }
            }

            if ($isAllowed) {
                $zip->extractTo(FCPATH, $filename);
            }
        }

        $zip->close();
        return true;
    }

    private function addDirToZip(string $dirPath, ZipArchive $zip, string $zipPath): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipPath . '/' . substr($filePath, strlen($dirPath) + 1);
                $relativePath = str_replace('\\', '/', $relativePath);

                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}
