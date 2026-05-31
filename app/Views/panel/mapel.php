<?php

/**
 * @var array $mapel
 * @var array $semua_guru
 */
?>
<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-5">
    <div>
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Mata Pelajaran & Relasi</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola data mata pelajaran dan atur Guru pengampunya.</p>
    </div>

    <form action="/panel/mapel/store" method="POST" class="flex gap-2 w-full sm:w-auto">
        <?= csrf_field() ?>
        <input type="text" name="nama_mapel" placeholder="Ketik nama mapel baru..." required
            class="px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none w-full sm:w-64 text-sm font-bold text-slate-700 uppercase shadow-sm transition">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-blue-600/20 flex items-center justify-center shrink-0 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Mapel
        </button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-800 font-bold border-b border-slate-200 uppercase text-[10px] tracking-wider">
                <tr>
                    <th class="px-6 py-4 w-16 text-center">No</th>
                    <th class="px-6 py-4 w-1/4">Mata Pelajaran</th>
                    <th class="px-6 py-4">Guru Pengampu</th>
                    <th class="px-6 py-4 w-48 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $no = 1;
                foreach ($mapel as $m): ?>
                    <tr class="hover:bg-blue-50/30 transition-colors h-16">
                        <td class="px-6 py-4 text-center font-medium"><?= $no++ ?></td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800 uppercase text-base"><?= esc($m['nama_mapel']) ?></div>

                            <?php
                            $badgePgClass = $m['total_pg'] > 0
                                ? 'bg-blue-50 text-blue-700 border-blue-200'
                                : 'bg-slate-50 text-slate-400 border-slate-200';

                            $badgeEssaiClass = $m['total_essai'] > 0
                                ? 'bg-indigo-50 text-indigo-700 border-indigo-200'
                                : 'bg-slate-50 text-slate-400 border-slate-200';
                            ?>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold border shadow-sm <?= $badgePgClass ?>" title="Total Soal Pilihan Ganda">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    PG: <?= $m['total_pg'] ?>
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold border shadow-sm <?= $badgeEssaiClass ?>" title="Total Soal Essai">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Essai: <?= $m['total_essai'] ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if (empty($m['guru_pengampu'])): ?>
                                <span class="inline-flex items-center text-[11px] text-red-500 bg-red-50 px-2.5 py-1 rounded-md border border-red-100 font-bold shadow-sm">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Belum ada pengampu
                                </span>
                            <?php else: ?>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($m['guru_pengampu'] as $gp): ?>
                                        <span class="inline-flex items-center text-[11px] text-emerald-700 bg-emerald-50 px-2.5 py-1.5 rounded-lg border border-emerald-200 font-bold shadow-sm uppercase tracking-wide">
                                            <svg class="w-3.5 h-3.5 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <?= esc($gp['nama_lengkap']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="bukaModalEdit(<?= htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8') ?>)" class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg border border-amber-200 transition shadow-sm" title="Edit Mapel">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>

                                <button type="button" onclick="bukaModalRelasi(<?= htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8') ?>)" class="p-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg border border-indigo-200 transition shadow-sm" title="Atur Guru Pengampu">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                    </svg>
                                </button>

                                <form action="/panel/mapel/delete/<?= $m['id'] ?>" method="POST" class="inline-block" id="formDelete<?= $m['id'] ?>">
                                    <?= csrf_field() ?>
                                    <button type="button" onclick="konfirmasiHapus(<?= $m['id'] ?>)" class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition shadow-sm" title="Hapus Mapel">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($mapel)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center text-slate-500 bg-slate-50">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <p class="font-bold text-slate-600">Belum ada data mata pelajaran.</p>
                            <p class="text-xs text-slate-400 mt-1">Silakan tambahkan mata pelajaran baru melalui form di atas.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalEdit" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform" id="modalEditContent">
        <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
                Edit Mata Pelajaran
            </h3>
            <button type="button" onclick="tutupModalEdit()" class="text-slate-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="formEditMapel" action="" method="POST">
            <?= csrf_field() ?>
            <div class="p-6">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Mata Pelajaran</label>
                <input type="text" name="nama_mapel" id="inputEditNamaMapel" required
                    class="px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none w-full text-sm font-bold uppercase text-slate-700 shadow-sm transition">
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="tutupModalEdit()" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-semibold rounded-xl hover:bg-slate-100 transition shadow-sm">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md transition font-bold flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalRelasi" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform scale-95 transition-transform" id="modalContent">

        <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
                Atur Guru Pengampu
            </h3>
            <button type="button" onclick="tutupModalRelasi()" class="text-slate-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="/panel/mapel/sync-guru" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="mapel_id" id="inputMapelId">

            <div class="p-6">
                <p class="text-sm text-slate-600 mb-4 bg-indigo-50 border border-indigo-100 p-3 rounded-lg leading-relaxed">
                    Centang guru yang diizinkan untuk menyusun soal pada mata pelajaran: <strong id="labelNamaMapel" class="text-indigo-700 block mt-1 uppercase text-lg"></strong>
                </p>

                <div class="max-h-60 overflow-y-auto space-y-1.5 custom-scrollbar border border-slate-200 rounded-xl p-2 bg-slate-50/50">
                    <?php if (empty($semua_guru)): ?>
                        <div class="text-center py-8 px-4">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <p class="text-sm font-bold text-slate-600">Belum Ada Data Guru</p>
                            <p class="text-xs text-slate-500 mt-1 mb-4 leading-relaxed">Anda harus menambahkan staff dengan hak akses <b>Guru</b> terlebih dahulu sebelum bisa melakukan sinkronisasi mata pelajaran.</p>

                            <a href="/panel/manajemen-staff" class="inline-flex items-center justify-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-lg transition shadow-sm">
                                Manajemen Staff
                                <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($semua_guru as $g): ?>
                            <label class="flex items-center p-3 bg-white hover:bg-blue-50 hover:border-blue-200 rounded-lg cursor-pointer border border-slate-200 transition shadow-sm group">
                                <input type="checkbox" name="guru_ids[]" value="<?= $g['id'] ?>" class="guru-checkbox w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 transition cursor-pointer">
                                <span class="ml-3 text-sm font-bold uppercase text-slate-700 w-full group-hover:text-indigo-700 transition-colors"><?= esc($g['nama_lengkap']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="tutupModalRelasi()" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-semibold rounded-xl hover:bg-slate-100 transition shadow-sm">Batal</button>

                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md shadow-indigo-600/30 transition font-bold flex items-center <?= empty($semua_guru) ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' ?>">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Simpan Relasi
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const modal = document.getElementById('modalRelasi');
    const modalContent = document.getElementById('modalContent');

    function bukaModalRelasi(dataMapel) {
        document.getElementById('inputMapelId').value = dataMapel.id;
        document.getElementById('labelNamaMapel').innerText = dataMapel.nama_mapel;

        document.querySelectorAll('.guru-checkbox').forEach(cb => cb.checked = false);

        if (dataMapel.guru_pengampu && dataMapel.guru_pengampu.length > 0) {
            let guruTerpilih = dataMapel.guru_pengampu.map(g => parseInt(g.id));
            guruTerpilih.forEach(gId => {
                const cb = document.querySelector(`.guru-checkbox[value="${gId}"]`);
                if (cb) cb.checked = true;
            });
        }

        toggleModal(modal, modalContent, true);
    }

    function tutupModalRelasi() {
        toggleModal(modal, modalContent, false);
    }

    const modalEdit = document.getElementById('modalEdit');
    const modalEditContent = document.getElementById('modalEditContent');
    const formEditMapel = document.getElementById('formEditMapel');

    function bukaModalEdit(dataMapel) {
        document.getElementById('inputEditNamaMapel').value = dataMapel.nama_mapel;
        formEditMapel.action = `/panel/mapel/update/${dataMapel.id}`;
        toggleModal(modalEdit, modalEditContent, true);
    }

    function tutupModalEdit() {
        toggleModal(modalEdit, modalEditContent, false);
    }

    function toggleModal(modalElem, contentElem, isShow) {
        if (isShow) {
            modalElem.classList.remove('hidden');
            modalElem.classList.add('flex');
            setTimeout(() => {
                modalElem.classList.remove('opacity-0');
                contentElem.classList.remove('scale-95');
            }, 10);
        } else {
            modalElem.classList.add('opacity-0');
            contentElem.classList.add('scale-95');
            setTimeout(() => {
                modalElem.classList.add('hidden');
                modalElem.classList.remove('flex');
            }, 300);
        }
    }

    function konfirmasiHapus(id) {
        Swal.fire({
            title: 'Hapus Mata Pelajaran?',
            text: "Pastikan mapel ini tidak sedang dipakai di Bank Soal maupun Jadwal Ujian.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formDelete' + id).submit();
            }
        })
    }
</script>
<?= $this->endSection() ?>