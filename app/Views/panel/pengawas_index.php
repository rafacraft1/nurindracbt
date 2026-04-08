<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800">Daftar Tugas Pengawas</h2>
    <p class="text-slate-500 text-sm mt-1">Pilih ruangan yang menjadi tanggung jawab Anda hari ini.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($jadwal as $j):
        $waktuMulai = strtotime($j['waktu_mulai']);
        $isToday = date('Y-m-d', $waktuMulai) == date('Y-m-d');

        $statusBadge = '';
        if ($j['status'] == 'draft') $statusBadge = '<span class="bg-slate-200 text-slate-700 px-2 py-1 rounded text-xs font-bold">DRAFT - Belum Di-build</span>';
        if ($j['status'] == 'ready') $statusBadge = '<span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">READY - Menunggu Token</span>';
        if ($j['status'] == 'active') $statusBadge = '<span class="bg-emerald-500 text-white shadow-[0_0_10px_#10b981] px-2 py-1 rounded text-xs font-bold">SEDANG BERJALAN</span>';
        if ($j['status'] == 'finished') $statusBadge = '<span class="bg-amber-100 text-amber-700 px-2 py-1 rounded text-xs font-bold">SELESAI</span>';
    ?>

        <div class="bg-white rounded-xl shadow-sm border <?= $isToday ? 'border-blue-400 shadow-blue-100' : 'border-slate-200' ?> overflow-hidden hover:shadow-md transition">
            <div class="p-5">
                <div class="flex justify-between items-start mb-4">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <?= date('d M Y', $waktuMulai) ?>
                    </div>
                    <?= $statusBadge ?>
                </div>

                <h3 class="text-xl font-bold text-slate-800 mb-1 uppercase truncate"><?= esc($j['nama_mapel']) ?></h3>
                <p class="text-indigo-600 font-bold mb-4">🚪 <?= esc($j['nama_ruangan']) ?></p>

                <div class="flex items-center text-sm text-slate-600 bg-slate-50 p-2 rounded-lg border border-slate-100 mb-5">
                    <span class="mr-2">⏰</span>
                    <strong><?= date('H:i', $waktuMulai) ?> WIB</strong>
                    <span class="mx-2 text-slate-300">|</span>
                    <span><?= $j['durasi'] ?> Menit</span>
                </div>

                <?php if ($j['status'] == 'draft'): ?>
                    <button disabled class="w-full bg-slate-200 text-slate-400 font-bold py-2.5 rounded-lg text-sm cursor-not-allowed">
                        Menunggu Panitia Build JSON
                    </button>
                <?php else: ?>
                    <a href="/panel/ruang-pengawas/monitor/<?= $j['id'] ?>" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg text-sm transition shadow block text-center">
                        Masuk ke Ruang Monitor 🚀
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($jadwal)): ?>
        <div class="col-span-full p-12 text-center border-2 border-dashed border-slate-300 rounded-xl bg-slate-50">
            <span class="text-4xl block mb-3">🏖️</span>
            <h3 class="text-lg font-bold text-slate-700">Tidak ada jadwal mengawas</h3>
            <p class="text-sm text-slate-500 mt-1">Anda belum di-plot sebagai pengawas di ruangan manapun.</p>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>