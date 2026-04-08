<?= $this->extend('layouts/panel') ?>
<?= $this->section('content') ?>

<div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
    <div>
        <a href="/panel/penilaian" class="text-blue-600 hover:underline text-sm font-semibold mb-2 inline-block">← Kembali ke Daftar</a>
        <h2 class="text-2xl font-bold text-slate-800">Detail Nilai: <?= esc($jadwal['nama_mapel']) ?></h2>
        <p class="text-slate-600 text-sm mt-1 font-semibold">Kelas: <?= esc($jadwal['tingkat'] . ' ' . $jadwal['jurusan']) ?> | Ruangan Acuan: <?= esc($jadwal['nama_ruangan']) ?></p>
    </div>

    <a href="/panel/penilaian/export/<?= $jadwal['id'] ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow-lg shadow-emerald-500/30 flex items-center">
        <span class="mr-2 text-lg">📊</span> Download Excel
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-100 text-slate-800 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-center w-12">No</th>
                    <th class="px-4 py-3 w-32">NISN</th>
                    <th class="px-4 py-3">Nama Siswa & Jenis Ujian</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center w-24">Nilai PG</th>
                    <th class="px-4 py-3 text-center w-24">Nilai Essai</th>
                    <th class="px-4 py-3 text-center w-24">Nilai Total</th>
                    <th class="px-4 py-3 text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $no = 1;
                foreach ($siswa as $s):
                    $pg = $s['nilai_pg'] ?? 0;
                    $essai = $s['nilai_essai'] ?? 0;
                    $total = ($pg + $essai) / 2;
                ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-4 text-center font-medium"><?= $no++ ?></td>
                        <td class="px-4 py-4 font-mono text-xs"><?= esc($s['nisn']) ?></td>
                        <td class="px-4 py-4">
                            <div class="font-bold text-slate-800 uppercase"><?= esc($s['nama_lengkap']) ?></div>
                            <?php if (!empty($s['keterangan_ujian'])): ?>
                                <div class="text-[10px] text-indigo-500 font-bold mt-1 uppercase tracking-wider">
                                    ▶ <?= esc($s['keterangan_ujian']) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <?php if ($s['status'] == 'completed'): ?>
                                <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded text-[10px] font-bold">Selesai</span>
                            <?php elseif ($s['status'] == 'progress'): ?>
                                <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded text-[10px] font-bold">Mengerjakan</span>
                            <?php else: ?>
                                <span class="bg-slate-100 text-slate-500 px-2 py-1 rounded text-[10px] font-bold">Belum</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-4 text-center font-bold text-blue-600"><?= number_format($pg, 1) ?></td>
                        <td class="px-4 py-4 text-center font-bold text-indigo-600"><?= number_format($essai, 1) ?></td>
                        <td class="px-4 py-4 text-center font-black text-slate-800"><?= number_format($total, 1) ?></td>
                        <td class="px-4 py-4 text-center">
                            <?php if ($s['status'] == 'completed' && !empty($s['actual_jadwal_id'])): ?>
                                <a href="/panel/penilaian/koreksi/<?= $s['actual_jadwal_id'] ?>/<?= $s['id'] ?>" class="inline-block px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded text-xs font-bold transition">
                                    ✍️ Koreksi
                                </a>
                            <?php else: ?>
                                <span class="text-[10px] text-slate-400 italic">Menunggu...</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>