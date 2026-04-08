<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Jadwal & Distribusi Pengawas</h2>
        <p class="text-slate-500 text-sm mt-1">Buat jadwal, plot pengawas anti-bentrok, dan generate JSON engine.</p>
    </div>

    <button onclick="bukaModalJadwal()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow flex items-center w-full lg:w-auto justify-center">
        <span class="mr-2">📅</span> Buat Jadwal Baru
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
            <thead class="bg-slate-100 text-slate-800 font-semibold uppercase text-[11px] tracking-wider select-none">

                <?php
                // ENGINE PEMBUAT LINK SORTING DINAMIS
                $buildSortLink = function ($colName) use ($search, $sortCol, $sortDir) {
                    $newDir = ($sortCol === $colName && $sortDir === 'ASC') ? 'DESC' : 'ASC';
                    $icon = '<span class="text-slate-300 ml-1 text-sm">↕</span>'; // Default Icon
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
                        <td class="px-4 py-3 text-center font-medium"><?= $no++ ?></td>

                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-800"><?= $hariMulai ?>, <?= date('d M Y', $waktuMulai) ?></div>
                            <div class="text-xs text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded inline-block mt-1">
                                ⏰ <?= date('H:i', $waktuMulai) ?> s/d <?= date('H:i', $waktuSelesai) ?>
                            </div>
                            <div class="text-[10px] font-bold text-slate-400 mt-0.5">⏱️ Durasi: <?= $j['durasi'] ?> Mnt</div>
                        </td>

                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-800 uppercase"><?= esc($j['nama_mapel']) ?></div>
                            <div class="text-[11px] text-slate-500 font-medium mt-1">
                                <?= esc($j['nama_ujian']) ?> • KLS: <?= esc($j['tingkat'] . ' ' . $j['jurusan']) ?>
                            </div>
                        </td>

                        <td class="px-4 py-3 text-center font-bold text-indigo-700">
                            <?= esc($j['nama_ruangan']) ?>
                        </td>

                        <td class="px-4 py-3">
                            <?php if ($j['pengawas_id']): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    👨‍🏫 <?= esc($j['nama_pengawas']) ?>
                                </span>
                                <button onclick='bukaModalPlot(<?= $j['id'] ?>, <?= $j['pengawas_id'] ?>)' class="ml-1 text-[10px] text-blue-500 hover:underline border border-blue-200 px-1.5 py-0.5 rounded bg-white">Ganti</button>
                            <?php else: ?>
                                <button onclick='bukaModalPlot(<?= $j['id'] ?>, null)' class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-red-100 text-red-700 border border-red-200 hover:bg-red-200 transition shadow-sm animate-pulse">
                                    ⚠️ Plot Pengawas
                                </button>
                            <?php endif; ?>
                        </td>

                        <td class="px-4 py-3 text-center">
                            <?php
                            $badgeStatus = ['draft' => 'bg-slate-200 text-slate-700', 'ready' => 'bg-blue-200 text-blue-800', 'active' => 'bg-emerald-500 text-white shadow-[0_0_10px_#10b981]', 'finished' => 'bg-amber-100 text-amber-800'];
                            $teksStatus = ['draft' => 'DRAFT', 'ready' => 'READY', 'active' => 'SEDANG UJIAN', 'finished' => 'SELESAI'];
                            ?>
                            <span class="px-2 py-1 rounded text-[10px] font-bold tracking-wider <?= $badgeStatus[$j['status']] ?>">
                                <?= $teksStatus[$j['status']] ?>
                            </span>
                        </td>

                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <?php if ($j['status'] === 'draft' || $j['status'] === 'ready'): ?>
                                    <form action="/panel/jadwal/generate-json/<?= $j['id'] ?>" method="POST" class="inline-block">
                                        <?= csrf_field() ?>
                                        <?php
                                        // Ekstrak Class agar Tailwind IntelliSense di VS Code tidak error
                                        $btnEngineClass = $j['status'] == 'draft'
                                            ? 'bg-indigo-600 hover:bg-indigo-700 text-white'
                                            : 'bg-indigo-100 hover:bg-indigo-200 text-indigo-700';
                                        ?>
                                        <button type="submit" onclick="this.innerHTML='⏳'" class="p-1.5 <?= $btnEngineClass ?> rounded transition shadow-sm" title="Generate File JSON Soal">
                                            ⚡ Build
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($j['status'] !== 'active'): ?>
                                    <button type="button" onclick='bukaModalEdit(<?= json_encode($j) ?>)' class="p-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded transition border border-amber-200" title="Edit Jadwal">
                                        ✏️
                                    </button>
                                    <form action="/panel/jadwal/delete/<?= $j['id'] ?>" method="POST" id="formDelete<?= $j['id'] ?>" class="inline-block">
                                        <?= csrf_field() ?>
                                        <button type="button" onclick="konfirmasiHapus(<?= $j['id'] ?>)" class="p-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded transition border border-red-200" title="Hapus Jadwal">
                                            🗑️
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-[9px] font-bold bg-slate-100 border border-slate-200 text-slate-400 px-2 py-1 rounded">TERKUNCI 🔒</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($jadwal)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500 border-dashed border-2 m-4 bg-slate-50">
                            <span class="text-3xl block mb-2">🔍</span>
                            Belum ada jadwal yang dibuat atau pencarian tidak ditemukan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 bg-slate-50 border-t flex flex-col md:flex-row justify-between items-center gap-4">
            <span class="text-xs font-semibold text-slate-500">
                Hal. <span class="text-slate-800"><?= $currentPage ?></span> dari <span class="text-slate-800"><?= $totalPages ?></span>
                (Total <span class="text-blue-600"><?= $totalData ?></span> Jadwal)
            </span>
            <div class="flex gap-1">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search ?? '') ?>&sort=<?= $sortCol ?>&dir=<?= $sortDir ?>" class="px-3 py-1.5 bg-white border border-slate-300 rounded-md text-sm text-slate-600 hover:bg-slate-100 font-medium transition shadow-sm">Prev</a>
                <?php endif; ?>

                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                for ($i = $startPage; $i <= $endPage; $i++):
                    $pageClass = ($i == $currentPage)
                        ? 'bg-blue-600 text-white border-blue-600 ring-2 ring-blue-200'
                        : 'bg-white border-slate-300 text-slate-600 hover:bg-slate-100';
                ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&sort=<?= $sortCol ?>&dir=<?= $sortDir ?>" class="px-3 py-1.5 border rounded-md text-sm font-medium transition shadow-sm <?= $pageClass ?>">
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

<div id="modalJadwal" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden transform scale-95 transition-transform" id="modalJadwalContent">
        <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white">Buat Jadwal Baru</h3>
            <button type="button" onclick="tutupModalJadwal()" class="text-slate-400 hover:text-white">✖</button>
        </div>

        <form action="/panel/jadwal/store" method="POST">
            <?= csrf_field() ?>
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Jenis Ujian</label>
                        <select name="jenis_ujian_id" required class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 bg-white">
                            <?php foreach ($jenis_ujian as $ju): ?>
                                <option value="<?= $ju['id'] ?>"><?= esc($ju['nama_ujian']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Mata Pelajaran</label>
                        <select name="mapel_id" required class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 bg-white font-bold text-slate-700">
                            <option value="">-- Pilih Mapel --</option>
                            <?php foreach ($mapel as $m): ?>
                                <option value="<?= $m['id'] ?>">
                                    <?= esc($m['nama_mapel']) ?> (PG: <?= $m['total_pg'] ?? 0 ?> | Essai: <?= $m['total_essai'] ?? 0 ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 bg-slate-50 p-3 rounded border border-slate-200">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase">Tingkat</label>
                        <input type="text" name="tingkat" placeholder="Misal: XII" required class="w-full px-3 py-1.5 border rounded text-sm uppercase focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase">Jurusan</label>
                        <input type="text" name="jurusan" placeholder="Misal: RPL" class="w-full px-3 py-1.5 border rounded text-sm uppercase focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase">Ruangan Ujian</label>
                        <select name="ruangan_id" required class="w-full px-3 py-1.5 border rounded text-sm bg-white font-bold text-indigo-700 focus:ring-2 focus:ring-blue-500">
                            <?php foreach ($ruangan as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= esc($r['nama_ruangan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-lg bg-blue-50/50 border border-blue-100">
                    <div>
                        <label class="block text-xs font-bold text-blue-800 mb-1 uppercase">Jam Ujian Dibuka</label>
                        <input type="datetime-local" name="waktu_mulai" required class="w-full px-3 py-2 border border-blue-200 rounded focus:ring-2 focus:ring-blue-500 font-mono text-[13px]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-red-800 mb-1 uppercase">Jam Ujian Ditutup</label>
                        <input type="datetime-local" name="waktu_selesai" required class="w-full px-3 py-2 border border-red-200 rounded focus:ring-2 focus:ring-red-500 font-mono text-[13px]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-emerald-800 mb-1 uppercase">Durasi Pengerjaan</label>
                        <div class="relative">
                            <input type="number" name="durasi" min="10" value="90" required class="w-full pl-3 pr-12 py-2 border border-emerald-200 rounded focus:ring-2 focus:ring-emerald-500 font-bold text-slate-700">
                            <span class="absolute right-3 top-2.5 text-xs font-bold text-emerald-600">Menit</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t flex justify-end gap-3">
                <button type="button" onclick="tutupModalJadwal()" class="px-4 py-2 border rounded-lg text-slate-700 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-md transition">Buat Jadwal</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEdit" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden transform scale-95 transition-transform" id="modalEditContent">
        <div class="bg-amber-500 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white">Edit Jadwal</h3>
            <button type="button" onclick="tutupModalEdit()" class="text-white/80 hover:text-white">✖</button>
        </div>

        <form id="formEditJadwal" method="POST">
            <?= csrf_field() ?>
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Jenis Ujian</label>
                        <select name="jenis_ujian_id" id="edit_jenis_ujian_id" required class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-amber-500 bg-white">
                            <?php foreach ($jenis_ujian as $ju): ?>
                                <option value="<?= $ju['id'] ?>"><?= esc($ju['nama_ujian']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Mata Pelajaran</label>
                        <select name="mapel_id" id="edit_mapel_id" required class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-amber-500 bg-white font-bold text-slate-700">
                            <option value="">-- Pilih Mapel --</option>
                            <?php foreach ($mapel as $m): ?>
                                <option value="<?= $m['id'] ?>">
                                    <?= esc($m['nama_mapel']) ?> (PG: <?= $m['total_pg'] ?? 0 ?> | Essai: <?= $m['total_essai'] ?? 0 ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 bg-amber-50/30 p-3 rounded border border-amber-100">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase">Tingkat</label>
                        <input type="text" name="tingkat" id="edit_tingkat" required class="w-full px-3 py-1.5 border rounded text-sm uppercase focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase">Jurusan</label>
                        <input type="text" name="jurusan" id="edit_jurusan" class="w-full px-3 py-1.5 border rounded text-sm uppercase focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase">Ruangan</label>
                        <select name="ruangan_id" id="edit_ruangan_id" required class="w-full px-3 py-1.5 border rounded text-sm bg-white font-bold text-indigo-700 focus:ring-2 focus:ring-amber-500">
                            <?php foreach ($ruangan as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= esc($r['nama_ruangan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-lg bg-amber-50/50 border border-amber-100">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1 uppercase">Jam Ujian Dibuka</label>
                        <input type="datetime-local" name="waktu_mulai" id="edit_waktu_mulai" required class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-amber-500 font-mono text-[13px]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1 uppercase">Jam Ujian Ditutup</label>
                        <input type="datetime-local" name="waktu_selesai" id="edit_waktu_selesai" required class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-amber-500 font-mono text-[13px]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1 uppercase">Durasi Mengerjakan</label>
                        <div class="relative">
                            <input type="number" name="durasi" id="edit_durasi" min="10" required class="w-full pl-3 pr-12 py-2 border rounded focus:ring-2 focus:ring-amber-500 font-bold text-slate-700">
                            <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-500">Menit</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t flex justify-end gap-3">
                <button type="button" onclick="tutupModalEdit()" class="px-4 py-2 border rounded-lg text-slate-700 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-6 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-bold shadow-md transition">Update Jadwal</button>
            </div>
        </form>
    </div>
</div>

<div id="modalPlot" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform scale-95 transition-transform" id="modalPlotContent">
        <div class="bg-emerald-600 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white">Plotting Pengawas</h3>
            <button type="button" onclick="tutupModalPlot()" class="text-emerald-100 hover:text-white">✖</button>
        </div>

        <form action="/panel/jadwal/plot-pengawas" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="jadwal_id" id="plotJadwalId">

            <div class="p-6">
                <div class="mb-4 bg-emerald-50 border border-emerald-200 p-3 rounded-lg text-xs text-emerald-800">
                    Pengawas dapat diubah kapan saja. Jika guru bentrok di ruangan lain pada jam yang sama, sistem akan otomatis menolak.
                </div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Guru Pengawas</label>
                <select name="pengawas_id" id="plotPengawasId" class="w-full px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-emerald-500 bg-white font-bold text-slate-700 outline-none">
                    <option value="">-- Kosongkan (Reset) --</option>
                    <?php foreach ($semua_guru as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= esc($g['nama_lengkap']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t flex justify-end gap-3">
                <button type="button" onclick="tutupModalPlot()" class="px-4 py-2 border rounded-lg text-slate-700 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-bold shadow-md transition">Simpan</button>
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
</script>
<?= $this->endSection() ?>