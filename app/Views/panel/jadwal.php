<?php

/**
 * @var string $sortCol
 * @var string $sortDir
 * @var string $search
 * @var int $currentPage
 * @var int $totalPages
 * @var int $totalData
 * @var array $jadwal
 * @var array $jenis_ujian
 * @var array $mapel
 * @var array $ruangan
 * @var array $semua_guru
 * @var array $listTingkat
 * @var array $listJurusan
 */
?>
<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Jadwal & Distribusi Pengawas</h2>
        <p class="text-slate-500 text-sm mt-1">Buat jadwal, atur parameter CBT, plot pengawas anti-bentrok, dan generate JSON.</p>
    </div>

    <button onclick="bukaModalJadwal()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow flex items-center w-full lg:w-auto justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        Buat Jadwal Baru
    </button>
</div>

<div class="bg-white p-4 rounded-t-xl border border-slate-200 border-b-0 flex justify-between items-center">
    <form action="/panel/jadwal" method="GET" class="flex w-full md:w-auto gap-2">
        <input type="hidden" name="sort" value="<?= esc($sortCol) ?>">
        <input type="hidden" name="dir" value="<?= esc($sortDir) ?>">

        <input type="text" name="search" value="<?= esc($search) ?>" placeholder="Cari Mapel atau Kelas..." class="w-full md:w-64 px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
        <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2 rounded-lg text-sm font-semibold transition shadow-sm">Cari</button>
        <?php if (!empty($search)): ?>
            <a href="/panel/jadwal" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-semibold transition border border-red-200 flex items-center justify-center">Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="bg-white rounded-b-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-100 text-slate-800 font-semibold uppercase text-[11px] tracking-wider select-none border-b border-slate-200">
                <?php
                $buildSortLink = function ($colName) use ($search, $sortCol, $sortDir) {
                    $newDir = ($sortCol === $colName && $sortDir === 'ASC') ? 'DESC' : 'ASC';
                    $icon = '<span class="text-slate-300 ml-1 text-sm">↕</span>';
                    if ($sortCol === $colName) {
                        $icon = $sortDir === 'ASC'
                            ? '<span class="text-blue-600 ml-1 text-sm">▲</span>'
                            : '<span class="text-blue-600 ml-1 text-sm">▼</span>';
                    }
                    $url = "?search=" . urlencode($search ?? '') . "&sort=" . $colName . "&dir=" . $newDir;
                    return ['url' => $url, 'icon' => $icon];
                };
                ?>
                <tr>
                    <th class="px-4 py-3 text-center">No</th>
                    <?php $linkWaktu = $buildSortLink('waktu'); ?>
                    <th class="px-4 py-3 hover:bg-slate-200 transition">
                        <a href="<?= $linkWaktu['url'] ?>" class="flex items-center gap-1">Rentang Waktu <?= $linkWaktu['icon'] ?></a>
                    </th>
                    <?php $linkMapel = $buildSortLink('mapel'); ?>
                    <th class="px-4 py-3 hover:bg-slate-200 transition">
                        <a href="<?= $linkMapel['url'] ?>" class="flex items-center gap-1">Mata Pelajaran <?= $linkMapel['icon'] ?></a>
                    </th>
                    <?php $linkRuang = $buildSortLink('ruangan'); ?>
                    <th class="px-4 py-3 text-center hover:bg-slate-200 transition">
                        <a href="<?= $linkRuang['url'] ?>" class="flex items-center justify-center gap-1">Ruangan <?= $linkRuang['icon'] ?></a>
                    </th>
                    <th class="px-4 py-3">Pengawas</th>
                    <?php $linkStatus = $buildSortLink('status'); ?>
                    <th class="px-4 py-3 text-center hover:bg-slate-200 transition">
                        <a href="<?= $linkStatus['url'] ?>" class="flex items-center justify-center gap-1">Status <?= $linkStatus['icon'] ?></a>
                    </th>
                    <th class="px-4 py-3 text-center">Aksi & Engine</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php
                $no = ($currentPage - 1) * 25 + 1;
                $daftarHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

                foreach ($jadwal as $j):
                    $waktuMulai = strtotime($j['waktu_mulai']);
                    $waktuSelesai = strtotime($j['waktu_selesai'] ?? date('Y-m-d H:i:s', $waktuMulai + ($j['durasi'] * 60)));
                    $hariMulai = $daftarHari[date('w', $waktuMulai)];
                ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-center font-medium text-slate-500"><?= $no++ ?></td>

                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-800"><?= $hariMulai ?>, <?= date('d M Y', $waktuMulai) ?></div>
                            <div class="text-xs text-blue-600 font-bold bg-blue-50 px-2 py-1 rounded-md inline-flex items-center mt-1 border border-blue-100 shadow-sm">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <?= date('H:i', $waktuMulai) ?> s/d <?= date('H:i', $waktuSelesai) ?>
                            </div>
                            <div class="text-[10px] font-bold text-slate-400 mt-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Durasi: <?= $j['durasi'] ?> Mnt
                            </div>
                        </td>

                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-800 uppercase tracking-wide"><?= esc($j['nama_mapel']) ?></div>
                            <div class="text-[11px] text-slate-500 font-bold mt-1 bg-slate-100 inline-block px-2 py-0.5 rounded border border-slate-200">
                                <?= esc($j['nama_ujian']) ?> • <?= esc($j['tingkat'] . ' ' . $j['jurusan']) ?>
                            </div>
                            <div class="flex gap-1.5 mt-1.5">
                                <?php if ($j['acak_soal']): ?>
                                    <span class="text-[9px] font-bold bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded border border-indigo-200" title="Soal Diacak">🔀 ACAK</span>
                                <?php endif; ?>
                                <?php if ($j['tampil_nilai']): ?>
                                    <span class="text-[9px] font-bold bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded border border-emerald-200" title="Nilai Langsung Tampil">📊 NILAI</span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <td class="px-4 py-3 text-center font-bold text-indigo-700">
                            <?= esc($j['nama_ruangan']) ?>
                        </td>

                        <td class="px-4 py-3">
                            <?php if ($j['pengawas_id']): ?>
                                <span class="inline-flex items-center px-2.5 py-1.5 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm uppercase tracking-wide">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <?= esc($j['nama_pengawas']) ?>
                                </span>
                                <button onclick='bukaModalPlot(<?= $j['id'] ?>, <?= $j['pengawas_id'] ?>)' class="ml-1 text-[10px] text-blue-600 hover:text-white hover:bg-blue-600 border border-blue-200 hover:border-blue-600 px-1.5 py-0.5 rounded bg-white transition shadow-sm">Ganti</button>
                            <?php else: ?>
                                <button onclick='bukaModalPlot(<?= $j['id'] ?>, null)' class="inline-flex items-center px-2.5 py-1.5 rounded-md text-[11px] font-bold bg-red-100 text-red-700 border border-red-200 hover:bg-red-200 transition shadow-sm animate-pulse">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    Plot Pengawas
                                </button>
                            <?php endif; ?>
                        </td>

                        <td class="px-4 py-3 text-center">
                            <?php
                            $badgeStatus = ['draft' => 'bg-slate-200 text-slate-700', 'ready' => 'bg-blue-200 text-blue-800', 'active' => 'bg-emerald-500 text-white shadow-[0_0_10px_#10b981]', 'finished' => 'bg-amber-100 text-amber-800'];
                            $teksStatus = ['draft' => 'DRAFT', 'ready' => 'READY', 'active' => 'SEDANG UJIAN', 'finished' => 'SELESAI'];
                            ?>
                            <span class="px-2.5 py-1.5 rounded-md text-[10px] font-bold tracking-wider <?= $badgeStatus[$j['status']] ?>">
                                <?= $teksStatus[$j['status']] ?>
                            </span>
                        </td>

                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <?php if ($j['status'] === 'draft' || $j['status'] === 'ready'): ?>
                                    <form action="/panel/jadwal/generate-json/<?= $j['id'] ?>" method="POST" class="inline-block">
                                        <?= csrf_field() ?>
                                        <?php
                                        $btnEngineClass = $j['status'] == 'draft'
                                            ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-md'
                                            : 'bg-indigo-100 hover:bg-indigo-200 text-indigo-700';
                                        ?>
                                        <button type="submit" onclick="this.innerHTML='<svg class=\'animate-spin w-4 h-4\' xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg>'" class="p-1.5 <?= $btnEngineClass ?> rounded-lg transition" title="Generate File JSON Soal">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($j['status'] !== 'active'): ?>
                                    <button type="button" onclick="bukaModalEdit(<?= htmlspecialchars(json_encode($j), ENT_QUOTES, 'UTF-8') ?>)" class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg transition border border-amber-200 shadow-sm" title="Edit Jadwal">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </button>
                                    <form action="/panel/jadwal/delete/<?= $j['id'] ?>" method="POST" id="formDelete<?= $j['id'] ?>" class="inline-block">
                                        <?= csrf_field() ?>
                                        <button type="button" onclick="konfirmasiHapus(<?= $j['id'] ?>)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition border border-red-200 shadow-sm" title="Hapus Jadwal">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="inline-flex items-center text-[9px] font-bold bg-slate-100 border border-slate-200 text-slate-400 px-2 py-1.5 rounded-md">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        TERKUNCI
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($jadwal)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-slate-500 bg-slate-50">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="font-bold text-slate-600">Belum ada jadwal yang dibuat.</p>
                            <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Buat Jadwal Baru" di atas.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 bg-slate-50 border-t flex flex-col md:flex-row justify-between items-center gap-4">
            <span class="text-xs font-semibold text-slate-500">
                Hal. <span class="text-slate-800 bg-white px-2 py-0.5 rounded border border-slate-300"><?= $currentPage ?></span> dari <span class="text-slate-800"><?= $totalPages ?></span>
                (Total <span class="text-blue-600 font-bold"><?= $totalData ?></span> Jadwal)
            </span>
            <div class="flex gap-1.5">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search ?? '') ?>&sort=<?= $sortCol ?>&dir=<?= $sortDir ?>" class="px-3 py-1.5 bg-white border border-slate-300 rounded-md text-sm text-slate-600 hover:bg-slate-100 font-medium transition shadow-sm">Prev</a>
                <?php endif; ?>

                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                for ($i = $startPage; $i <= $endPage; $i++):
                    $pageClass = ($i == $currentPage)
                        ? 'bg-blue-600 text-white border-blue-600 ring-2 ring-blue-600/20'
                        : 'bg-white border-slate-300 text-slate-600 hover:bg-slate-50';
                ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&sort=<?= $sortCol ?>&dir=<?= $sortDir ?>" class="px-3 py-1.5 border rounded-md text-sm font-bold transition shadow-sm <?= $pageClass ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search ?? '') ?>&sort=<?= $sortCol ?>&dir=<?= $sortDir ?>" class="px-3 py-1.5 bg-white border border-slate-300 rounded-md text-sm text-slate-600 hover:bg-slate-100 font-medium transition shadow-sm">Next</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<div id="modalJadwal" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0 py-6">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden transform scale-95 transition-transform flex flex-col max-h-full" id="modalJadwalContent">
        <div class="bg-slate-800 px-6 py-4 flex justify-between items-center shrink-0">
            <h3 class="font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Buat Jadwal Baru
            </h3>
            <button type="button" onclick="tutupModalJadwal()" class="text-slate-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="/panel/jadwal/store" method="POST" class="overflow-y-auto custom-scrollbar">
            <?= csrf_field() ?>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Ujian</label>
                        <select name="jenis_ujian_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white font-bold text-slate-700 shadow-sm transition">
                            <?php foreach ($jenis_ujian as $ju): ?>
                                <option value="<?= $ju['id'] ?>"><?= esc($ju['nama_ujian']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Mata Pelajaran</label>
                        <select name="mapel_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white font-bold text-slate-700 shadow-sm transition">
                            <option value="">-- Pilih Mapel --</option>
                            <?php foreach ($mapel as $m): ?>
                                <option value="<?= $m['id'] ?>">
                                    <?= esc($m['nama_mapel']) ?> (PG: <?= $m['total_pg'] ?? 0 ?> | Essai: <?= $m['total_essai'] ?? 0 ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Tingkat</label>
                        <input list="dataTingkat" type="text" name="tingkat" placeholder="Pilih / Ketik..." required autocomplete="off" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm uppercase font-bold focus:ring-2 focus:ring-blue-500 bg-white transition">
                        <datalist id="dataTingkat">
                            <?php foreach ($listTingkat as $t): ?>
                                <option value="<?= esc($t['tingkat']) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Jurusan</label>
                        <input list="dataJurusan" type="text" name="jurusan" placeholder="Pilih / Ketik..." autocomplete="off" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm uppercase font-bold focus:ring-2 focus:ring-blue-500 bg-white transition">
                        <datalist id="dataJurusan">
                            <?php foreach ($listJurusan as $j): ?>
                                <option value="<?= esc($j['jurusan']) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Ruangan Ujian</label>
                        <select name="ruangan_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white font-bold text-indigo-700 focus:ring-2 focus:ring-blue-500 transition">
                            <?php foreach ($ruangan as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= esc($r['nama_ruangan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                    <div class="flex items-center text-[10px] text-blue-600 font-bold uppercase tracking-wider mb-3">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pengaturan Waktu Ujian (Auto-Calculate)
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-blue-800 mb-1.5 uppercase tracking-wide">Jam Ujian Dibuka</label>
                            <input type="datetime-local" name="waktu_mulai" id="create_waktu_mulai" required class="w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-[13px] bg-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-800 mb-1.5 uppercase tracking-wide">Durasi Pengerjaan</label>
                            <div class="relative">
                                <input type="number" name="durasi" id="create_durasi" min="10" value="90" required class="w-full pl-3 pr-12 py-2 border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 font-bold text-slate-700 bg-white">
                                <span class="absolute right-3 top-2.5 text-xs font-bold text-emerald-600">Menit</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-red-800 mb-1.5 uppercase tracking-wide">Jam Ujian Ditutup</label>
                            <input type="datetime-local" name="waktu_selesai" id="create_waktu_selesai" required class="w-full px-3 py-2 border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500 font-mono text-[13px] bg-white">
                            <p class="text-[9px] text-red-600 mt-1 font-medium leading-tight">*Waktu ditutup otomatis dihitung dari Jam Dibuka + Durasi + 15 Menit Toleransi.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-center space-x-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-indigo-300 cursor-pointer transition shadow-sm group">
                        <input type="checkbox" name="acak_soal" value="1" checked class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 cursor-pointer">
                        <div>
                            <p class="text-sm font-bold text-slate-800 group-hover:text-indigo-700 transition">Acak Urutan Soal</p>
                            <p class="text-[10px] text-slate-500 leading-tight mt-0.5">Setiap siswa akan menerima soal dengan urutan berbeda.</p>
                        </div>
                    </label>

                    <label class="flex items-center space-x-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-emerald-300 cursor-pointer transition shadow-sm group">
                        <input type="checkbox" name="tampil_nilai" value="1" class="w-5 h-5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                        <div>
                            <p class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition">Tampilkan Nilai</p>
                            <p class="text-[10px] text-slate-500 leading-tight mt-0.5">Siswa dapat melihat nilai langsung setelah selesai ujian.</p>
                        </div>
                    </label>
                </div>

            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 shrink-0">
                <button type="button" onclick="tutupModalJadwal()" class="px-5 py-2.5 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-100 font-semibold transition">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold shadow-md shadow-blue-500/30 transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalEdit" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0 py-6">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden transform scale-95 transition-transform flex flex-col max-h-full" id="modalEditContent">
        <div class="bg-amber-500 px-6 py-4 flex justify-between items-center shrink-0">
            <h3 class="font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
                Edit Jadwal
            </h3>
            <button type="button" onclick="tutupModalEdit()" class="text-white/80 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="formEditJadwal" method="POST" class="overflow-y-auto custom-scrollbar">
            <?= csrf_field() ?>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Ujian</label>
                        <select name="jenis_ujian_id" id="edit_jenis_ujian_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 bg-white font-bold text-slate-700 transition">
                            <?php foreach ($jenis_ujian as $ju): ?>
                                <option value="<?= $ju['id'] ?>"><?= esc($ju['nama_ujian']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Mata Pelajaran</label>
                        <select name="mapel_id" id="edit_mapel_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 bg-white font-bold text-slate-700 transition">
                            <option value="">-- Pilih Mapel --</option>
                            <?php foreach ($mapel as $m): ?>
                                <option value="<?= $m['id'] ?>">
                                    <?= esc($m['nama_mapel']) ?> (PG: <?= $m['total_pg'] ?? 0 ?> | Essai: <?= $m['total_essai'] ?? 0 ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 bg-amber-50/30 p-4 rounded-xl border border-amber-100">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Tingkat</label>
                        <input list="dataTingkatEdit" type="text" name="tingkat" id="edit_tingkat" placeholder="Pilih / Ketik..." required autocomplete="off" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm uppercase font-bold focus:ring-2 focus:ring-amber-500 bg-white transition">
                        <datalist id="dataTingkatEdit">
                            <?php foreach ($listTingkat as $t): ?>
                                <option value="<?= esc($t['tingkat']) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Jurusan</label>
                        <input list="dataJurusanEdit" type="text" name="jurusan" id="edit_jurusan" placeholder="Pilih / Ketik..." autocomplete="off" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm uppercase font-bold focus:ring-2 focus:ring-amber-500 bg-white transition">
                        <datalist id="dataJurusanEdit">
                            <?php foreach ($listJurusan as $j): ?>
                                <option value="<?= esc($j['jurusan']) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Ruangan</label>
                        <select name="ruangan_id" id="edit_ruangan_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white font-bold text-indigo-700 focus:ring-2 focus:ring-amber-500 transition">
                            <?php foreach ($ruangan as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= esc($r['nama_ruangan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-100">
                    <div class="flex items-center text-[10px] text-amber-700 font-bold uppercase tracking-wider mb-3">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pengaturan Waktu Ujian (Auto-Calculate)
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-800 mb-1.5 uppercase tracking-wide">Jam Ujian Dibuka</label>
                            <input type="datetime-local" name="waktu_mulai" id="edit_waktu_mulai" required class="w-full px-3 py-2 border border-amber-200 rounded-lg focus:ring-2 focus:ring-amber-500 font-mono text-[13px] bg-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-800 mb-1.5 uppercase tracking-wide">Durasi Pengerjaan</label>
                            <div class="relative">
                                <input type="number" name="durasi" id="edit_durasi" min="10" required class="w-full pl-3 pr-12 py-2 border border-amber-200 rounded-lg focus:ring-2 focus:ring-amber-500 font-bold text-slate-700 bg-white">
                                <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-500">Menit</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-800 mb-1.5 uppercase tracking-wide">Jam Ujian Ditutup</label>
                            <input type="datetime-local" name="waktu_selesai" id="edit_waktu_selesai" required class="w-full px-3 py-2 border border-amber-200 rounded-lg focus:ring-2 focus:ring-amber-500 font-mono text-[13px] bg-white">
                            <p class="text-[9px] text-amber-700 mt-1 font-medium leading-tight">*Waktu ditutup otomatis dihitung dari Jam Dibuka + Durasi + 15 Menit Toleransi.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-center space-x-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-indigo-300 cursor-pointer transition shadow-sm group">
                        <input type="checkbox" name="acak_soal" id="edit_acak_soal" value="1" class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 cursor-pointer">
                        <div>
                            <p class="text-sm font-bold text-slate-800 group-hover:text-indigo-700 transition">Acak Urutan Soal</p>
                            <p class="text-[10px] text-slate-500 leading-tight mt-0.5">Setiap siswa akan menerima soal dengan urutan berbeda.</p>
                        </div>
                    </label>

                    <label class="flex items-center space-x-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-emerald-300 cursor-pointer transition shadow-sm group">
                        <input type="checkbox" name="tampil_nilai" id="edit_tampil_nilai" value="1" class="w-5 h-5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                        <div>
                            <p class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition">Tampilkan Nilai</p>
                            <p class="text-[10px] text-slate-500 leading-tight mt-0.5">Siswa dapat melihat nilai langsung setelah selesai ujian.</p>
                        </div>
                    </label>
                </div>

            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 shrink-0">
                <button type="button" onclick="tutupModalEdit()" class="px-5 py-2.5 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-100 font-semibold transition">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-amber-500 text-white rounded-xl hover:bg-amber-600 font-bold shadow-md shadow-amber-500/30 transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalPlot" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform scale-95 transition-transform" id="modalPlotContent">
        <div class="bg-emerald-600 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Plot Pengawas
            </h3>
            <button type="button" onclick="tutupModalPlot()" class="text-emerald-200 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="/panel/jadwal/plot-pengawas" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="jadwal_id" id="plotJadwalId">

            <div class="p-6">
                <div class="mb-5 bg-emerald-50 border border-emerald-200 p-3.5 rounded-xl text-xs text-emerald-800 leading-relaxed shadow-sm">
                    Pengawas dapat diubah kapan saja. Jika guru bentrok di ruangan lain pada jam yang sama, sistem akan otomatis menolak.
                </div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Guru Pengawas</label>
                <select name="pengawas_id" id="plotPengawasId" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 bg-white font-bold text-slate-700 outline-none shadow-sm transition">
                    <option value="">-- Kosongkan (Reset) --</option>
                    <?php foreach ($semua_guru as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= esc($g['nama_lengkap']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="tutupModalPlot()" class="px-5 py-2.5 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-100 font-semibold transition">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-bold shadow-md shadow-emerald-600/30 transition flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function toggleModal(modal, content, isShow) {
        if (isShow) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
            }, 10);
        } else {
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }
    }

    const mJadwal = document.getElementById('modalJadwal');
    const cJadwal = document.getElementById('modalJadwalContent');

    function bukaModalJadwal() {
        toggleModal(mJadwal, cJadwal, true);
    }

    function tutupModalJadwal() {
        toggleModal(mJadwal, cJadwal, false);
    }

    const mEdit = document.getElementById('modalEdit');
    const cEdit = document.getElementById('modalEditContent');

    function bukaModalEdit(jadwal) {
        document.getElementById('formEditJadwal').action = '/panel/jadwal/update/' + jadwal.id;
        document.getElementById('edit_jenis_ujian_id').value = jadwal.jenis_ujian_id;
        document.getElementById('edit_mapel_id').value = jadwal.mapel_id;
        document.getElementById('edit_tingkat').value = jadwal.tingkat;
        document.getElementById('edit_jurusan').value = jadwal.jurusan;
        document.getElementById('edit_ruangan_id').value = jadwal.ruangan_id;
        document.getElementById('edit_durasi').value = jadwal.durasi;

        document.getElementById('edit_acak_soal').checked = (jadwal.acak_soal == 1);
        document.getElementById('edit_tampil_nilai').checked = (jadwal.tampil_nilai == 1);

        if (jadwal.waktu_mulai) document.getElementById('edit_waktu_mulai').value = jadwal.waktu_mulai.substring(0, 16).replace(' ', 'T');
        if (jadwal.waktu_selesai) document.getElementById('edit_waktu_selesai').value = jadwal.waktu_selesai.substring(0, 16).replace(' ', 'T');

        toggleModal(mEdit, cEdit, true);
    }

    function tutupModalEdit() {
        toggleModal(mEdit, cEdit, false);
    }

    const mPlot = document.getElementById('modalPlot');
    const cPlot = document.getElementById('modalPlotContent');

    function bukaModalPlot(jadwalId, currentPengawasId) {
        document.getElementById('plotJadwalId').value = jadwalId;
        document.getElementById('plotPengawasId').value = currentPengawasId || '';
        toggleModal(mPlot, cPlot, true);
    }

    function tutupModalPlot() {
        toggleModal(mPlot, cPlot, false);
    }

    function konfirmasiHapus(id) {
        Swal.fire({
            title: 'Hapus Jadwal?',
            text: "Jadwal dan file JSON soal akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('formDelete' + id).submit();
        })
    }

    function autoCalcWaktuSelesai(prefix) {
        let valMulai = document.getElementById(prefix + '_waktu_mulai').value;
        let valDurasi = parseInt(document.getElementById(prefix + '_durasi').value);

        if (valMulai && !isNaN(valDurasi)) {
            let dateObj = new window.Date(valMulai);

            // Tambahkan durasi murni + 15 Menit Toleransi Keterlambatan standar CBT
            dateObj.setMinutes(dateObj.getMinutes() + valDurasi + 15);

            let y = dateObj.getFullYear();
            let m = String(dateObj.getMonth() + 1).padStart(2, '0');
            let d = String(dateObj.getDate()).padStart(2, '0');
            let h = String(dateObj.getHours()).padStart(2, '0');
            let min = String(dateObj.getMinutes()).padStart(2, '0');

            document.getElementById(prefix + '_waktu_selesai').value = `${y}-${m}-${d}T${h}:${min}`;
        }
    }

    ['create', 'edit'].forEach(prefix => {
        let elMulai = document.getElementById(prefix + '_waktu_mulai');
        let elDurasi = document.getElementById(prefix + '_durasi');

        if (elMulai && elDurasi) {
            elMulai.addEventListener('change', () => autoCalcWaktuSelesai(prefix));
            elDurasi.addEventListener('input', () => autoCalcWaktuSelesai(prefix));
        }
    });
</script>
<?= $this->endSection() ?>