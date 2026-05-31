<?php

/**
 * @var array $jadwal
 * @var array $siswa
 * @var array $listRombel
 * @var string|null $rombelFilter
 */
?>
<?= $this->extend('layouts/panel') ?>
<?= $this->section('content') ?>

<div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 border-b border-slate-200 pb-5">
    <div>
        <a href="/panel/penilaian" class="text-blue-600 hover:text-blue-800 text-sm font-bold mb-2 flex items-center group transition">
            <svg class="w-4 h-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Laporan
        </a>
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Detail Nilai: <?= esc($jadwal['nama_mapel']) ?></h2>

        <div class="flex flex-wrap items-center gap-3 mt-2">
            <span class="bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-md text-xs font-bold border border-indigo-100 uppercase tracking-wide">
                Jurusan: <?= esc($jadwal['tingkat'] . ' ' . $jadwal['jurusan']) ?>
            </span>
            <span class="bg-slate-50 text-slate-600 px-2.5 py-1 rounded-md text-xs font-medium border border-slate-200 flex items-center">
                <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <?= esc($jadwal['nama_ruangan']) ?>
            </span>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
        <form method="GET" class="w-full sm:w-auto" id="formFilterRombel">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                </div>
                <select name="rombel" onchange="document.getElementById('formFilterRombel').submit()" class="w-full sm:w-48 pl-9 pr-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white font-bold text-slate-700 shadow-sm cursor-pointer outline-none transition text-sm">
                    <option value="">Semua Kelas</option>
                    <?php foreach ($listRombel as $r): ?>
                        <option value="<?= esc($r['rombel']) ?>" <?= ($rombelFilter == $r['rombel']) ? 'selected' : '' ?>>
                            Kelas: <?= esc($jadwal['tingkat'] . ' ' . $jadwal['jurusan'] . ' ' . $r['rombel']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <a href="/panel/penilaian/export/<?= $jadwal['id'] ?><?= !empty($rombelFilter) ? '?rombel=' . urlencode($rombelFilter) : '' ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/30 transition flex items-center transform hover:-translate-y-0.5 w-full sm:w-auto justify-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download Excel
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600 min-w-[800px]">
            <thead class="bg-slate-50 text-slate-800 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-center w-16">No</th>
                    <th class="px-6 py-4 w-32">NISN</th>
                    <th class="px-6 py-4">Nama Siswa & Kelas</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center w-24">Nilai PG</th>
                    <th class="px-6 py-4 text-center w-24">Nilai Essai</th>
                    <th class="px-6 py-4 text-center w-24">Nilai Akhir</th>
                    <th class="px-6 py-4 text-center w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $no = 1;
                foreach ($siswa as $s):
                    $pg = $s['nilai_pg'] ?? 0;
                    $essai = $s['nilai_essai'] ?? 0;
                    $total = ($pg + $essai) / 2;
                ?>
                    <tr class="hover:bg-blue-50/30 transition-colors h-16">
                        <td class="px-6 py-4 text-center font-medium"><?= $no++ ?></td>
                        <td class="px-6 py-4 font-mono text-xs font-bold text-slate-500"><?= esc($s['nisn']) ?></td>

                        <td class="px-6 py-4">
                            <div class="font-black text-slate-800 uppercase tracking-wide"><?= esc($s['nama_lengkap']) ?></div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] bg-slate-100 text-slate-600 font-bold px-1.5 py-0.5 rounded border border-slate-200">
                                    <?= esc($s['tingkat'] . ' ' . $s['jurusan'] . ' ' . $s['rombel']) ?>
                                </span>
                                <?php if (!empty($s['keterangan_ujian'])): ?>
                                    <span class="text-[10px] text-indigo-500 font-bold uppercase tracking-wider flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                                        </svg>
                                        <?= esc($s['keterangan_ujian']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <?php if ($s['status'] == 'completed'): ?>
                                <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider shadow-sm">Selesai</span>
                            <?php elseif ($s['status'] == 'progress'): ?>
                                <span class="bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider shadow-sm animate-pulse">Ujian Aktif</span>
                            <?php else: ?>
                                <span class="bg-slate-50 text-slate-500 border border-slate-200 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider">Belum Masuk</span>
                            <?php endif; ?>
                        </td>

                        <td class="px-6 py-4 text-center font-black text-blue-600 text-base"><?= number_format($pg, 1) ?></td>
                        <td class="px-6 py-4 text-center font-black text-indigo-600 text-base"><?= number_format($essai, 1) ?></td>

                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-12 h-8 bg-slate-800 text-white font-black rounded-lg shadow-sm">
                                <?= number_format($total, 1) ?>
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <?php if ($s['status'] == 'completed' && !empty($s['actual_jadwal_id'])):
                                $urlKoreksi = "/panel/penilaian/koreksi/{$s['actual_jadwal_id']}/{$s['id']}";
                                if (!empty($rombelFilter)) {
                                    $urlKoreksi .= "?rombel=" . urlencode($rombelFilter);
                                }
                            ?>
                                <a href="<?= $urlKoreksi ?>" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-bold transition shadow-sm transform hover:-translate-y-0.5">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    Koreksi
                                </a>
                            <?php else: ?>
                                <span class="text-[10px] text-slate-300 font-bold uppercase tracking-wide">-- Menunggu --</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($siswa)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-500 bg-slate-50">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Tidak ada siswa yang terdaftar di pencarian kelas ini.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>