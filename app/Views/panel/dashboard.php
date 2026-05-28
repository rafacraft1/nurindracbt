<?php

/**
 * @var int $total_siswa
 * @var int $total_ruang
 * @var int $total_guru
 * @var int $ujian_aktif
 */
?>
<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<?php
$dbDash = \Config\Database::connect();
$pDash = $dbDash->table('pengaturan')->where('id', 1)->get()->getRowArray();
$tahunAjaran = $pDash['tahun_ajaran'] ?? '-';
$semester = isset($pDash['semester']) ? strtoupper($pDash['semester']) : '-';
?>

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Dashboard</h2>
        <p class="text-slate-500 text-sm mt-1">Selamat datang kembali, <?= session()->get('nama_lengkap') ?>.</p>
    </div>
    <div class="bg-blue-50 border border-blue-200 px-4 py-2 rounded-xl flex items-center gap-3 shadow-sm shrink-0">
        <span class="text-xl">📅</span>
        <div class="text-xs">
            <p class="font-bold text-blue-800">Tahun Ajaran: <?= esc($tahunAjaran) ?></p>
            <p class="font-semibold text-blue-600 uppercase">Semester: <?= esc($semester) ?></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1">Total Siswa</p>
            <h3 class="text-3xl font-bold text-slate-800"><?= number_format($total_siswa) ?></h3>
        </div>
        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl">
            👥
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1">Total Ruangan</p>
            <h3 class="text-3xl font-bold text-slate-800"><?= number_format($total_ruang) ?></h3>
        </div>
        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-xl">
            🚪
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1">Total Guru</p>
            <h3 class="text-3xl font-bold text-slate-800"><?= number_format($total_guru) ?></h3>
        </div>
        <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xl">
            👨‍🏫
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1">Ujian Aktif</p>
            <h3 class="text-3xl font-bold text-slate-800"><?= number_format($ujian_aktif) ?></h3>
        </div>
        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center text-xl animate-pulse">
            🔥
        </div>
    </div>

</div>

<div class="bg-white rounded-xl shadow-sm border border-blue-100 p-6 border-l-4 border-l-blue-500">
    <h4 class="font-bold text-slate-800 mb-2">Informasi Hak Akses Anda</h4>
    <p class="text-sm text-slate-600 leading-relaxed">
        <?php if (session()->get('role') === 'admin'): ?>
            Anda login sebagai <strong>Super Admin</strong>. Anda memiliki kendali absolut atas seluruh sistem, termasuk memanajemen hak akses kepanitiaan untuk staf lain.
        <?php elseif (session()->get('is_panitia') == 1): ?>
            Anda login sebagai <strong>Panitia Ujian</strong>. Anda dapat mengelola master data (siswa, ruangan, jadwal) dan mengkoordinasikan distribusi pengawas ujian.
        <?php else: ?>
            Anda login sebagai <strong>Guru Mata Pelajaran</strong>. Anda dapat mengelola bank soal, mendistribusikan soal ujian, serta melakukan koreksi dan penilaian hasil ujian secara mandiri.
        <?php endif; ?>
    </p>
</div>

<?= $this->endSection() ?>