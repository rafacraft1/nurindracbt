<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        'auth'          => \App\Filters\AuthFilter::class,
        'watermark'     => \App\Filters\WatermarkFilter::class,
        'throttle'      => \App\Filters\ThrottleFilter::class, // TAMBAHAN: Mendaftarkan Anti-DDoS
    ];

    public array $required = [
        'before' => [
            'forcehttps',
            'pagecache',
        ],
        'after' => [
            'pagecache',
            'performance',
            'toolbar',
        ],
    ];

    // FIX EXPLOIT: Menyalakan pelindung Form & Header secara global
    public array $globals = [
        'before' => [
            'csrf',           // Wajib aktif agar form terproteksi
            'secureheaders',  // Wajib aktif agar terhindar dari Clickjacking & sniffing XSS
        ],
        'after' => [
            'watermark' => ['except' => ['panel/cetak_kartu*', 'panel/export*', '*/cetak*']],
        ],
    ];

    public array $methods = [];

    // FIX DDOS RISK: Pasang pembatasan Request per IP hanya pada rute yang rawan di-spam
    public array $filters = [
        'throttle' => [
            'before' => [
                'login',
                'auth/*',
                'ujian/simpan-jawaban-ajax' // Melindungi DB dari spamming Autosave
            ]
        ]
    ];
}
