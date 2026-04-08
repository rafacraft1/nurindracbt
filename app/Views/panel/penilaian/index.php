<?= $this->extend('layouts/panel') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800">Laporan & Penilaian</h2>
    <p class="text-slate-500 text-sm mt-1">Pilih kelas/jadwal untuk melihat nilai dan mengoreksi essai.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($jadwal as $j): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition">
            <div class="flex justify-between items-start mb-4">
                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-bold rounded uppercase tracking-wider border border-indigo-100">
                    <?= $j['status'] === 'finished' ? 'Selesai' : 'Aktif' ?>
                </span>
                <span class="text-xs font-bold text-slate-400"><?= date('d M Y', strtotime($j['waktu_mulai'])) ?></span>
            </div>
            <h3 class="text-lg font-bold text-slate-800 uppercase leading-tight mb-1"><?= esc($j['nama_mapel']) ?></h3>
            <p class="text-sm font-semibold text-slate-600 mb-4">Kelas: <?= esc($j['tingkat'] . ' ' . $j['jurusan']) ?> <span class="text-xs text-slate-400 font-normal">(&#128205; <?= esc($j['nama_ruangan']) ?>)</span></p>

            <a href="/panel/penilaian/detail/<?= $j['id'] ?>" class="block text-center w-full bg-slate-800 hover:bg-slate-900 text-white py-2.5 rounded-lg text-sm font-bold transition shadow-sm">
                Lihat Nilai & Koreksi
            </a>
        </div>
    <?php endforeach; ?>

    <?php if (empty($jadwal)): ?>
        <div class="col-span-full p-12 text-center bg-slate-50 rounded-xl border border-dashed border-slate-300">
            <span class="text-4xl mb-3 block">📭</span>
            <p class="text-slate-500 font-bold">Belum ada jadwal ujian di kelas Anda.</p>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>