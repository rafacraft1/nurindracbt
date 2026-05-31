<?php

/**
 * @var string $title
 * @var array $jadwal
 */

$dbPengawas = \Config\Database::connect();
$pengaturanPengawas = $dbPengawas->table('pengaturan')->where('id', 1)->get()->getRowArray();
$zonaWaktu = $pengaturanPengawas['zona_waktu'] ?? 'Asia/Jakarta';

$labelZona = 'WIB';
if ($zonaWaktu === 'Asia/Makassar') $labelZona = 'WITA';
if ($zonaWaktu === 'Asia/Jayapura') $labelZona = 'WIT';
?>
<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6 border-b border-slate-200 pb-5">
    <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Daftar Tugas Pengawas</h2>
    <p class="text-slate-500 text-sm mt-1">Pilih ruangan ujian yang menjadi tanggung jawab Anda hari ini.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($jadwal as $j):
        $waktuMulai = strtotime($j['waktu_mulai']);
        $waktuSelesai = strtotime($j['waktu_selesai']);
        $isToday = date('Y-m-d', $waktuMulai) == date('Y-m-d');

        $statusBadge = '';
        if ($j['status'] == 'draft') $statusBadge = '<span class="bg-slate-200 text-slate-700 px-2.5 py-1 rounded-md text-[10px] uppercase font-bold tracking-wider border border-slate-300">DRAFT - Belum Build</span>';
        if ($j['status'] == 'ready') $statusBadge = '<span class="bg-blue-100 text-blue-700 px-2.5 py-1 rounded-md text-[10px] uppercase font-bold tracking-wider border border-blue-200">READY - Menunggu Waktu</span>';
        if ($j['status'] == 'active') $statusBadge = '<span class="bg-emerald-500 text-white shadow-[0_0_10px_#10b981] px-2.5 py-1 rounded-md text-[10px] uppercase font-bold tracking-wider">SEDANG BERJALAN</span>';
        if ($j['status'] == 'finished') $statusBadge = '<span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-md text-[10px] uppercase font-bold tracking-wider border border-amber-200">SELESAI</span>';
    ?>

        <div class="bg-white rounded-2xl shadow-sm border <?= $isToday ? 'border-blue-400 shadow-blue-100' : 'border-slate-200' ?> overflow-hidden hover:shadow-md transition flex flex-col justify-between group">
            <div class="p-6">
                <div class="flex justify-between items-start mb-5">
                    <div class="text-xs font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded border border-slate-100">
                        <?= date('d M Y', $waktuMulai) ?>
                    </div>
                    <?= $statusBadge ?>
                </div>

                <h3 class="text-xl font-black text-slate-800 mb-2 uppercase truncate group-hover:text-blue-600 transition-colors"><?= esc($j['nama_mapel']) ?></h3>

                <div class="flex items-center text-indigo-700 font-bold mb-5">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <?= esc($j['nama_ruangan']) ?>
                </div>

                <div class="flex items-center text-sm font-bold text-slate-600 bg-slate-50 px-3 py-2.5 rounded-xl border border-slate-200 mb-5">
                    <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><?= date('H:i', $waktuMulai) ?> <?= $labelZona ?></span>
                    <span class="mx-3 text-slate-300">|</span>
                    <span><?= $j['durasi'] ?> Menit</span>
                </div>
            </div>

            <div class="px-6 pb-6 mt-auto">
                <?php if ($j['status'] == 'draft'): ?>
                    <button disabled class="w-full bg-slate-100 text-slate-400 font-bold py-3 rounded-xl text-sm cursor-not-allowed border border-slate-200">
                        Menunggu Panitia Build JSON
                    </button>
                <?php elseif ($j['status'] == 'finished' || time() > $waktuSelesai): ?>
                    <button disabled class="w-full bg-red-50 text-red-500 font-bold py-3 rounded-xl text-sm cursor-not-allowed border border-red-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Ujian Telah Ditutup
                    </button>
                <?php elseif (time() < strtotime('-2 hours', $waktuMulai)): ?>
                    <button disabled class="w-full bg-slate-100 text-slate-400 font-bold py-3 rounded-xl text-sm cursor-not-allowed border border-slate-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ruangan Belum Dibuka
                    </button>
                <?php else: ?>
                    <a href="/panel/ruang-pengawas/monitor/<?= $j['id'] ?>" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl text-sm transition shadow-lg shadow-blue-500/30 flex items-center justify-center transform hover:-translate-y-0.5">
                        Masuk Ruang Monitor
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($jadwal)): ?>
        <div class="col-span-full py-16 px-6 text-center border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50">
            <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-xl font-bold text-slate-700">Tidak Ada Tugas Pengawas</h3>
            <p class="text-sm text-slate-500 mt-2">Anda belum di-plot sebagai pengawas ujian di ruangan manapun.</p>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>