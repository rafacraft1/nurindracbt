<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Mata Pelajaran & Relasi</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola data mata pelajaran dan atur Guru pengampunya.</p>
    </div>

    <form action="/panel/mapel/store" method="POST" class="flex gap-2 w-full sm:w-auto">
        <?= csrf_field() ?>
        <input type="text" name="nama_mapel" placeholder="Ketik nama mapel baru..." required
            class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none w-full sm:w-64 text-sm">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition shadow">
            Tambah
        </button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-800 font-semibold border-b border-slate-200 uppercase text-xs">
                <tr>
                    <th class="px-6 py-4 w-16 text-center">No</th>
                    <th class="px-6 py-4 w-1/4">Mata Pelajaran</th>
                    <th class="px-6 py-4">Guru Pengampu</th>
                    <th class="px-6 py-4 w-40 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $no = 1;
                foreach ($mapel as $m): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-center font-medium"><?= $no++ ?></td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800 uppercase"><?= esc($m['nama_mapel']) ?></div>

                            <?php
                            // Ekstrak class ke variabel agar Tailwind IntelliSense di VS Code tidak error/bingung
                            $badgePgClass = $m['total_pg'] > 0
                                ? 'bg-blue-50 text-blue-700 border-blue-200'
                                : 'bg-slate-100 text-slate-400 border-slate-200';

                            $badgeEssaiClass = $m['total_essai'] > 0
                                ? 'bg-indigo-50 text-indigo-700 border-indigo-200'
                                : 'bg-slate-100 text-slate-400 border-slate-200';
                            ?>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border <?= $badgePgClass ?>" title="Total Soal Pilihan Ganda">
                                    📝 PG: <?= $m['total_pg'] ?>
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border <?= $badgeEssaiClass ?>" title="Total Soal Essai">
                                    ✍️ Essai: <?= $m['total_essai'] ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if (empty($m['guru_pengampu'])): ?>
                                <span class="text-xs text-red-500 bg-red-50 px-2 py-1 rounded-md border border-red-100">Belum ada pengampu</span>
                            <?php else: ?>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($m['guru_pengampu'] as $gp): ?>
                                        <span class="text-xs text-blue-700 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-200 font-medium">
                                            <?= esc($gp['nama_lengkap']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button"
                                    onclick='bukaModalRelasi(<?= $m['id'] ?>, <?= json_encode(array_column($m['guru_pengampu'], 'id')) ?>, "<?= esc($m['nama_mapel']) ?>")'
                                    class="p-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-md transition" title="Atur Guru Pengampu">
                                    ⚙️
                                </button>
                                <form action="/panel/mapel/delete/<?= $m['id'] ?>" method="POST" class="inline-block" id="formDelete<?= $m['id'] ?>">
                                    <?= csrf_field() ?>
                                    <button type="button" onclick="konfirmasiHapus(<?= $m['id'] ?>)" class="p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-md transition" title="Hapus Mapel">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($mapel)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500">Belum ada data mata pelajaran.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalRelasi" class="fixed inset-0 bg-slate-900/50 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform scale-95 transition-transform" id="modalContent">

        <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white">Atur Guru Pengampu</h3>
            <button type="button" onclick="tutupModalRelasi()" class="text-slate-400 hover:text-white transition">✖</button>
        </div>

        <form action="/panel/mapel/sync-guru" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="mapel_id" id="inputMapelId">

            <div class="p-6">
                <p class="text-sm text-slate-600 mb-4">Pilih guru yang berhak membuat soal untuk mapel: <strong id="labelNamaMapel" class="text-blue-600"></strong></p>

                <div class="max-h-60 overflow-y-auto space-y-2 custom-scrollbar border border-slate-200 rounded-lg p-2">
                    <?php foreach ($semua_guru as $g): ?>
                        <label class="flex items-center p-3 hover:bg-slate-50 rounded cursor-pointer border border-transparent hover:border-slate-200 transition">
                            <input type="checkbox" name="guru_ids[]" value="<?= $g['id'] ?>" class="guru-checkbox w-5 h-5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                            <span class="ml-3 text-sm font-medium text-slate-700"><?= esc($g['nama_lengkap']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="tutupModalRelasi()" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow transition font-medium">Simpan Relasi</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const modal = document.getElementById('modalRelasi');
    const modalContent = document.getElementById('modalContent');

    function bukaModalRelasi(mapelId, guruTerpilih, namaMapel) {
        // Set Data ke Modal
        document.getElementById('inputMapelId').value = mapelId;
        document.getElementById('labelNamaMapel').innerText = namaMapel;

        // Reset semua checkbox
        document.querySelectorAll('.guru-checkbox').forEach(cb => cb.checked = false);

        // Centang guru yang sudah ada di database
        guruTerpilih.forEach(gId => {
            const cb = document.querySelector(`.guru-checkbox[value="${gId}"]`);
            if (cb) cb.checked = true;
        });

        // Tampilkan animasi Modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
        }, 10);
    }

    function tutupModalRelasi() {
        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    function konfirmasiHapus(id) {
        Swal.fire({
            title: 'Hapus Mata Pelajaran?',
            text: "Data soal yang terhubung mungkin akan ikut terhapus atau kehilangan relasi!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formDelete' + id).submit();
            }
        })
    }
</script>
<?= $this->endSection() ?>