<?php

/**
 * @var int $total_siswa
 * @var int $total_ruang
 * @var int $total_guru
 * @var int $ujian_aktif
 * @var array $pengaturan
 * @var array $jadwal_aktif
 * @var array $jadwal_terdekat
 * @var string $role
 * @var int $is_panitia
 * @var int $total_bank_soal
 */
?>
<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<?php
$tahunAjaran  = $pengaturan['tahun_ajaran'] ?? '-';
$semester     = isset($pengaturan['semester']) ? strtoupper($pengaturan['semester']) : '-';
$namaUser     = session()->get('nama_lengkap') ?? 'Pengguna';
$isSuperAdmin = ($role === 'admin');
$isPanitia    = ($is_panitia == 1);
$isGuru       = ($role === 'guru' && !$isPanitia);

// ==============================================================================
// ENGINE SMART GREETING (Timezone-Aware)
// Membaca zona waktu dari pengaturan sekolah agar sapaan 100% akurat
// ==============================================================================
$zonaWaktu = $pengaturan['zona_waktu'] ?? 'Asia/Jakarta';
$waktuSistem = new \DateTime('now', new \DateTimeZone($zonaWaktu));
$jam = (int)$waktuSistem->format('H');

if ($jam >= 3 && $jam < 11) {
    $sapaan = 'Selamat Pagi';
    $iconSapaan = '<span class="text-amber-500 text-lg leading-none drop-shadow-sm">🌅</span>';
} elseif ($jam >= 11 && $jam < 15) {
    $sapaan = 'Selamat Siang';
    $iconSapaan = '<span class="text-yellow-500 text-lg leading-none drop-shadow-sm">☀️</span>';
} elseif ($jam >= 15 && $jam < 18) {
    $sapaan = 'Selamat Sore';
    $iconSapaan = '<span class="text-orange-500 text-lg leading-none drop-shadow-sm">🌇</span>';
} else {
    $sapaan = 'Selamat Malam';
    $iconSapaan = '<span class="text-indigo-400 text-lg leading-none drop-shadow-sm">🌙</span>';
}
?>

<div class="mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
    <div>
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Dashboard</h2>
        <p class="text-slate-500 text-sm mt-1.5 flex items-center gap-1.5">
            <?= $iconSapaan ?>
            <span><?= $sapaan ?>, <strong class="text-slate-700"><?= esc($namaUser) ?></strong>.</span>
        </p>
    </div>
    <div class="bg-blue-50/50 border border-blue-100 px-5 py-3 rounded-2xl flex items-center gap-4 shadow-sm shrink-0 backdrop-blur-sm">
        <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <div class="text-sm">
            <p class="font-bold text-slate-800">Tahun Ajaran <span class="text-blue-600"><?= esc($tahunAjaran) ?></span></p>
            <p class="font-semibold text-slate-500">Semester <?= esc($semester) ?></p>
        </div>
    </div>
</div>

<div class="mb-8 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg border border-blue-700 p-6 flex flex-col sm:flex-row items-center justify-between gap-4 relative overflow-hidden">
    <div class="absolute -right-10 -top-10 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>

    <div class="flex items-center gap-4 z-10">
        <div class="p-3 bg-white/20 rounded-xl text-white backdrop-blur-md">
            <?php if ($isSuperAdmin): ?>
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            <?php elseif ($isPanitia): ?>
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            <?php else: ?>
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            <?php endif; ?>
        </div>
        <div>
            <h4 class="font-bold text-white text-lg">Informasi Hak Akses</h4>
            <p class="text-blue-100 text-sm leading-relaxed max-w-2xl mt-0.5">
                <?php if ($isSuperAdmin): ?>
                    Anda login sebagai <strong class="text-white">Super Admin</strong>. Anda memiliki kendali penuh atas sistem ini, termasuk penugasan hak panitia.
                <?php elseif ($isPanitia): ?>
                    Anda login sebagai <strong class="text-white">Panitia Ujian</strong>. Anda dapat mengelola data master (siswa, ruangan) dan menjadwalkan ujian.
                <?php else: ?>
                    Anda login sebagai <strong class="text-white">Guru Mata Pelajaran</strong>. Anda dapat menyusun bank soal, token ujian, serta melihat nilai koreksi.
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<?php if ($isSuperAdmin || $isPanitia): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center justify-between hover:shadow-md hover:border-blue-300 transition-all group">
            <div>
                <p class="text-sm font-bold text-slate-500 mb-1 group-hover:text-blue-600 transition-colors">Total Siswa</p>
                <h3 class="text-3xl font-black text-slate-800"><?= number_format($total_siswa) ?></h3>
            </div>
            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center justify-between hover:shadow-md hover:border-emerald-300 transition-all group">
            <div>
                <p class="text-sm font-bold text-slate-500 mb-1 group-hover:text-emerald-600 transition-colors">Total Ruangan</p>
                <h3 class="text-3xl font-black text-slate-800"><?= number_format($total_ruang) ?></h3>
            </div>
            <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center justify-between hover:shadow-md hover:border-purple-300 transition-all group">
            <div>
                <p class="text-sm font-bold text-slate-500 mb-1 group-hover:text-purple-600 transition-colors">Total Guru</p>
                <h3 class="text-3xl font-black text-slate-800"><?= number_format($total_guru) ?></h3>
            </div>
            <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center justify-between hover:shadow-md hover:border-amber-400 transition-all group">
            <div>
                <p class="text-sm font-bold text-slate-500 mb-1 group-hover:text-amber-600 transition-colors">Ujian Aktif</p>
                <h3 class="text-3xl font-black text-slate-800"><?= number_format($ujian_aktif) ?></h3>
            </div>
            <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300 shadow-sm relative">
                <?php if ($ujian_aktif > 0): ?>
                    <span class="absolute top-0 right-0 flex h-3 w-3 -mt-1 -mr-1">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                <?php endif; ?>
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center justify-between hover:shadow-md hover:border-indigo-300 transition-all group">
            <div>
                <p class="text-sm font-bold text-slate-500 mb-1 group-hover:text-indigo-600 transition-colors">Bank Soal Saya</p>
                <h3 class="text-3xl font-black text-slate-800"><?= number_format($total_bank_soal) ?></h3>
            </div>
            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center justify-between hover:shadow-md hover:border-amber-400 transition-all group">
            <div>
                <p class="text-sm font-bold text-slate-500 mb-1 group-hover:text-amber-600 transition-colors">Ujian Sistem (Aktif)</p>
                <h3 class="text-3xl font-black text-slate-800"><?= number_format($ujian_aktif) ?></h3>
            </div>
            <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300 shadow-sm relative">
                <?php if ($ujian_aktif > 0): ?>
                    <span class="absolute top-0 right-0 flex h-3 w-3 -mt-1 -mr-1">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                <?php endif; ?>
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <h3 class="font-bold text-slate-800">Quick Actions</h3>
            </div>
            <div class="p-4 flex flex-col gap-3">

                <?php if ($isSuperAdmin || $isPanitia): ?>
                    <a href="/panel/jadwal" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50 transition-all group">
                        <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm group-hover:text-blue-700">Buat Jadwal Baru</p>
                            <p class="text-[11px] text-slate-500">Atur sesi jadwal & kelas</p>
                        </div>
                    </a>

                    <a href="/panel/ruangan" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50 transition-all group">
                        <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm group-hover:text-emerald-700">Plotting Ruangan</p>
                            <p class="text-[11px] text-slate-500">Isi peserta ke dalam ruang ujian</p>
                        </div>
                    </a>
                <?php endif; ?>

                <?php if ($isGuru): ?>
                    <a href="/panel/bank-soal" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50 transition-all group">
                        <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm group-hover:text-indigo-700">Manajemen Soal</p>
                            <p class="text-[11px] text-slate-500">Input pertanyaan & buat paket baru</p>
                        </div>
                    </a>

                    <a href="/panel/penilaian" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50 transition-all group">
                        <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm group-hover:text-blue-700">Koreksi Jawaban</p>
                            <p class="text-[11px] text-slate-500">Lihat nilai siswa & koreksi manual</p>
                        </div>
                    </a>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">

        <?php if ($ujian_aktif > 0 && !empty($jadwal_aktif)): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-amber-200 overflow-hidden relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-400 to-orange-500"></div>
                <div class="p-5 border-b border-amber-100 bg-amber-50/50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-3 w-3 mr-1">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                        </span>
                        <h3 class="font-bold text-amber-900">Live Ujian Berlangsung</h3>
                    </div>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 font-semibold uppercase text-[10px] tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3">Jadwal / Mapel Aktif</th>
                                <th class="px-5 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($jadwal_aktif as $ja): ?>
                                <tr class="hover:bg-amber-50/30 transition-colors">
                                    <td class="px-5 py-3">
                                        <p class="font-bold text-slate-800 text-sm uppercase"><?= esc($ja['nama_mapel']) ?></p>
                                        <p class="text-[11px] text-slate-500 font-semibold mt-0.5">
                                            Kelas: <strong class="text-slate-600"><?= esc($ja['tingkat'] . ' ' . $ja['jurusan']) ?></strong>
                                            <span class="mx-1">&bull;</span>
                                            ⏱ <?= date('H:i', strtotime((string)$ja['waktu_mulai'])) ?> - <?= date('H:i', strtotime((string)$ja['waktu_selesai'])) ?> WIB
                                        </p>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 font-bold rounded-full text-[10px] shadow-sm uppercase tracking-wide">
                                            LIVE AKTIF
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="font-bold text-slate-800">Riwayat Jadwal Ujian Terbaru</h3>
                </div>
                <?php if ($isSuperAdmin || $isPanitia): ?>
                    <a href="/panel/jadwal" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition">Lihat Semua →</a>
                <?php endif; ?>
            </div>

            <div class="p-0 overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-semibold uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Mata Pelajaran & Waktu</th>
                            <th class="px-5 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($jadwal_terdekat)): ?>
                            <tr>
                                <td colspan="2" class="px-6 py-12 text-center text-slate-500">
                                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="font-medium text-slate-600">Belum ada data jadwal.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($jadwal_terdekat as $jt): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-3">
                                        <p class="font-bold text-slate-800 uppercase"><?= esc($jt['nama_mapel'] ?? 'UJIAN') ?></p>
                                        <p class="text-[11px] text-slate-500 font-semibold mt-0.5">
                                            <?= date('d M Y', strtotime((string)$jt['waktu_mulai'])) ?>
                                            <span class="mx-1">&bull;</span>
                                            ⏱ <?= date('H:i', strtotime((string)$jt['waktu_mulai'])) ?> - <?= date('H:i', strtotime((string)$jt['waktu_selesai'])) ?> WIB
                                        </p>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <?php if (($jt['status'] ?? '') === 'finished'): ?>
                                            <span class="px-2.5 py-1 bg-slate-100 text-slate-500 font-bold rounded-md text-[10px] uppercase border border-slate-200 shadow-sm">Selesai</span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 font-bold rounded-md text-[10px] uppercase border border-indigo-200 shadow-sm"><?= esc($jt['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>