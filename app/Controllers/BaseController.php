<?php

/**
 * ============================================================================
 * CBT PRO - ENTERPRISE EDITION
 * ============================================================================
 */

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $helpers = [];

    // Variabel Global dengan Explicit Type Declaration
    protected ?array $pengaturanGlobal = null;
    protected string $tahunAktif = '2025/2026';
    protected string $smtAktif = 'ganjil';

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // ==========================================================================
        // 1. INJEKSI KEAMANAN GLOBAL (HTTP SECURITY HEADERS)
        // ==========================================================================

        // Mencegah Clickjacking (Aplikasi tidak bisa disisipkan ke <iframe> web hacker)
        $this->response->setHeader('X-Frame-Options', 'SAMEORIGIN');

        // Memaksa browser mengaktifkan filter XSS (Cross-Site Scripting)
        $this->response->setHeader('X-XSS-Protection', '1; mode=block');

        // Mencegah MIME-Sniffing (mencegah browser mengeksekusi file gambar/teks sebagai script)
        $this->response->setHeader('X-Content-Type-Options', 'nosniff');

        // Mencegah data 'Referrer' bocor ke website lain saat klik link keluar
        $this->response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // (Opsional) Memaksa koneksi menggunakan HTTPS selama 1 tahun penuh jika SSL tersedia
        $this->response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // ==========================================================================
        // 2. INISIASI PENGATURAN GLOBAL DAN ZONA WAKTU
        // ==========================================================================
        $db = \Config\Database::connect();
        $this->pengaturanGlobal = $db->table('pengaturan')->where('id', 1)->get()->getRowArray();

        if ($this->pengaturanGlobal) {
            if (!empty($this->pengaturanGlobal['zona_waktu'])) {
                date_default_timezone_set($this->pengaturanGlobal['zona_waktu']);
            }

            // Casting (string) untuk memastikan Strict Type Intelephense tetap aman
            $this->tahunAktif = (string)($this->pengaturanGlobal['tahun_ajaran'] ?? '2025/2026');
            $this->smtAktif   = (string)($this->pengaturanGlobal['semester'] ?? 'ganjil');
        }
    }
}
