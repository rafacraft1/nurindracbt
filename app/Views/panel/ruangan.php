<?php

/**
 * @var array $ruangan
 * @var array $siswa
 */
?>
<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Manajemen Ruangan</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola data ruangan ujian dan isi peserta ke dalamnya.</p>
    </div>

    <button onclick="bukaModalRuangan()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow flex items-center w-full lg:w-auto justify-center">
        <span class="mr-2">➕</span> Tambah Ruangan
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-100 text-slate-800 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-center w-16">No</th>
                    <th class="px-4 py-3">Nama Ruangan</th>
                    <th class="px-4 py-3 text-center w-40">Kapasitas Terisi</th>
                    <th class="px-4 py-3 text-center w-64">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $no = 1;
                foreach ($ruangan as $r): ?>
                    <tr class="hover:bg-slate-50 transition-colors h-16">
                        <td class="px-4 py-4 text-center font-medium text-slate-500"><?= $no++ ?></td>

                        <td class="px-4 py-4 min-w-[250px] align-middle">
                            <div id="view-ruangan-<?= $r['id'] ?>" class="flex items-center group">
                                <span class="font-bold text-slate-800 text-base uppercase cursor-pointer hover:text-blue-600 transition border-b-2 border-transparent hover:border-blue-400" onclick="enableEditRuangan(<?= $r['id'] ?>)" title="Klik untuk edit">
                                    <?= esc($r['nama_ruangan']) ?>
                                </span>
                                <button type="button" onclick="enableEditRuangan(<?= $r['id'] ?>)" class="ml-2 text-slate-400 hover:text-blue-600 opacity-0 group-hover:opacity-100 transition focus:opacity-100 p-1 rounded-full hover:bg-slate-100" title="Edit Nama Ruangan">
                                    ✏️
                                </button>
                            </div>

                            <form action="/panel/ruangan/update/<?= $r['id'] ?>" method="POST" id="form-ruangan-<?= $r['id'] ?>" class="hidden items-center gap-1 w-full max-w-sm">
                                <?= csrf_field() ?>
                                <input type="text" name="nama_ruangan" id="input-ruangan-<?= $r['id'] ?>" value="<?= esc($r['nama_ruangan']) ?>" class="flex-1 px-3 py-1.5 text-sm font-bold uppercase text-slate-800 border-2 border-blue-500 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-200 shadow-sm transition" required onkeydown="if(event.key === 'Escape') disableEditRuangan(<?= $r['id'] ?>)">

                                <button type="submit" class="p-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-sm transition flex-shrink-0" title="Simpan (Enter)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                                <button type="button" onclick="disableEditRuangan(<?= $r['id'] ?>)" class="p-1.5 bg-slate-200 text-slate-600 rounded-md hover:bg-slate-300 transition flex-shrink-0" title="Batal (Esc)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>

                        <td class="px-4 py-4 text-center">
                            <?php if ($r['jumlah_siswa'] > 0): ?>
                                <button onclick="bukaModalListSiswa(<?= $r['id'] ?>, '<?= esc($r['nama_ruangan'], 'js') ?>')" class="px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold rounded-full text-xs shadow-sm transition transform hover:scale-105 active:scale-95 cursor-pointer">
                                    👥 <?= $r['jumlah_siswa'] ?> Siswa
                                </button>
                            <?php else: ?>
                                <span class="px-3 py-1 bg-slate-100 text-slate-400 font-bold rounded-full text-xs border border-slate-200">
                                    Kosong
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick='bukaModalPlot(<?= $r['id'] ?>, "<?= esc($r['nama_ruangan'], 'js') ?>")' class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg transition shadow-sm font-semibold text-xs flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg> Isi Siswa
                                </button>

                                <?php if ($r['jumlah_siswa'] > 0): ?>
                                    <form action="/panel/ruangan/kosongkan/<?= $r['id'] ?>" method="POST" class="inline-block" id="formKosong<?= $r['id'] ?>">
                                        <?= csrf_field() ?>
                                        <button type="button" onclick="konfirmasi('Kosongkan Ruangan?', 'Seluruh siswa akan dikeluarkan dari ruangan ini.', 'formKosong<?= $r['id'] ?>')" class="px-2 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-200 rounded-lg transition shadow-sm" title="Kosongkan Ruangan">
                                            🧹
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <form action="/panel/ruangan/delete/<?= $r['id'] ?>" method="POST" class="inline-block" id="formDelete<?= $r['id'] ?>">
                                    <?= csrf_field() ?>
                                    <button type="button" onclick="konfirmasi('Hapus Ruangan?', 'Data ruangan ini akan dihapus permanen.', 'formDelete<?= $r['id'] ?>')" class="px-2 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition shadow-sm" title="Hapus Ruangan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($ruangan)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 bg-slate-50">
                            <span class="text-3xl block mb-2">🚪</span>
                            Belum ada data ruangan yang dibuat.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalRuangan" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform scale-95 transition-transform" id="modalRuanganContent">
        <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white">Tambah Ruangan</h3>
            <button type="button" onclick="tutupModalRuangan()" class="text-slate-400 hover:text-white transition">✖</button>
        </div>
        <form action="/panel/ruangan/store" method="POST">
            <?= csrf_field() ?>
            <div class="p-6">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Ruangan</label>
                <input type="text" name="nama_ruangan" placeholder="Misal: LAB KOMPUTER 1" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none uppercase font-bold text-slate-800 transition">
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="tutupModalRuangan()" class="px-4 py-2 border rounded-lg text-slate-700 hover:bg-slate-200 font-semibold transition">Batal</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-md transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="modalPlot" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0 py-6">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden transform scale-95 transition-transform flex flex-col max-h-full" id="modalPlotContent">

        <div class="bg-emerald-600 px-6 py-4 flex justify-between items-center shrink-0">
            <h3 class="font-bold text-white flex items-center">
                <span class="text-xl mr-2">👥</span> Isi Ruangan: <span id="labelNamaRuangan" class="ml-1 text-emerald-100 uppercase underline decoration-2 underline-offset-4"></span>
            </h3>
            <button type="button" onclick="tutupModalPlot()" class="text-emerald-200 hover:text-white transition text-xl">✖</button>
        </div>

        <form action="/panel/ruangan/plot-siswa" method="POST" class="flex flex-col flex-1 overflow-hidden">
            <?= csrf_field() ?>
            <input type="hidden" name="ruangan_id" id="inputPlotRuanganId">

            <div class="p-4 bg-emerald-50 border-b border-emerald-100 shrink-0">
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>

                        <input type="text" id="searchSiswa" oninput="filterSiswa()" placeholder="Ketik Kelas (Misal: XII RPL 1) atau Nama..." class="w-full pl-9 pr-10 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none shadow-sm transition">

                        <div id="loadingSpinner" class="absolute inset-y-0 right-0 items-center pr-3 pointer-events-none hidden">
                            <svg class="animate-spin h-5 w-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <button type="button" onclick="checkSemuaTerfilter()" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-lg text-xs font-bold transition shadow-sm whitespace-nowrap">
                        ☑️ Centang Tampil
                    </button>
                </div>
            </div>

            <div class="overflow-y-auto flex-1 p-2 bg-slate-100/50 custom-scrollbar" id="containerDaftarSiswa">
            </div>

            <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-between items-center shrink-0 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.05)]">
                <p class="text-[10px] text-slate-400 w-1/2 leading-tight">Siswa yang tidak dicentang akan <b class="text-red-500">dikeluarkan</b> dari ruangan ini secara otomatis.</p>
                <div class="flex gap-3">
                    <button type="button" onclick="tutupModalPlot()" class="px-4 py-2 border rounded-lg text-slate-700 hover:bg-slate-100 font-semibold transition">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-bold shadow-lg shadow-emerald-600/30 transition">💾 Simpan Penghuni</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="modalListSiswa" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0 py-6">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform scale-95 transition-transform flex flex-col max-h-full" id="modalListSiswaContent">
        <div class="bg-blue-600 px-6 py-4 flex justify-between items-center shrink-0">
            <h3 class="font-bold text-white flex items-center">
                <span class="text-xl mr-2">📋</span> Penghuni: <span id="labelDetailRuangan" class="ml-1 text-blue-100 uppercase underline decoration-2 underline-offset-4"></span>
            </h3>
            <button type="button" onclick="tutupModalListSiswa()" class="text-blue-200 hover:text-white transition text-xl">✖</button>
        </div>

        <div class="overflow-y-auto flex-1 p-4 bg-slate-50 custom-scrollbar" id="containerListSiswa">
        </div>

        <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end shrink-0">
            <button type="button" onclick="tutupModalListSiswa()" class="px-6 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 font-bold transition">Tutup</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // 1. Modul SweetAlert Konfirmasi
    function konfirmasi(judul, teks, formId) {
        Swal.fire({
            title: judul,
            text: teks,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Lanjutkan!'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById(formId).submit();
        })
    }

    // 2. Modul Modal Tambah Ruangan
    const mRuangan = document.getElementById('modalRuangan');
    const cRuangan = document.getElementById('modalRuanganContent');

    function bukaModalRuangan() {
        toggleModal(mRuangan, cRuangan, true);
    }

    function tutupModalRuangan() {
        toggleModal(mRuangan, cRuangan, false);
    }

    // 3. Helper Anti XSS
    function escapeHtml(text) {
        return text == null ? '' : String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // 4. Modul Dynamic Plot Siswa
    const mPlot = document.getElementById('modalPlot');
    const cPlot = document.getElementById('modalPlotContent');
    const dataSiswa = <?= json_encode($siswa) ?>;

    function bukaModalPlot(ruanganId, namaRuangan) {
        document.getElementById('inputPlotRuanganId').value = ruanganId;
        document.getElementById('labelNamaRuangan').innerText = namaRuangan;

        document.getElementById('searchSiswa').value = '';

        // Reset Spinner (CSS Tailwind Conflict Fix)
        document.getElementById('loadingSpinner').classList.add('hidden');
        document.getElementById('loadingSpinner').classList.remove('flex');

        let htmlList = '';

        dataSiswa.forEach(s => {
            let isChecked = (s.ruangan_id == ruanganId) ? 'checked' : '';
            let badge = '';

            if (s.ruangan_id && s.ruangan_id != ruanganId) {
                badge = '<span class="text-[9px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded ml-2 font-bold border border-red-200">Di Ruangan Lain</span>';
            } else if (s.ruangan_id == ruanganId) {
                badge = '<span class="text-[9px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded ml-2 font-bold border border-emerald-200">Sudah Masuk</span>';
            }

            let kelasStr = s.tingkat + ' ' + s.jurusan + ' ' + s.rombel;
            let keywordRaw = s.nama_lengkap + ' ' + kelasStr;

            htmlList += `
            <label class="siswa-item flex items-center p-3 mb-1 bg-white border border-slate-200 rounded-lg hover:border-emerald-400 hover:bg-emerald-50 cursor-pointer transition-colors shadow-sm" data-search="${escapeHtml(keywordRaw.toLowerCase())}">
                <input type="checkbox" name="siswa_ids[]" value="${s.id}" ${isChecked} class="w-5 h-5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                <div class="ml-4 flex-1">
                    <p class="text-sm font-bold text-slate-800 uppercase tracking-wide nama-teks">${escapeHtml(s.nama_lengkap)} ${badge}</p>
                    <p class="text-[11px] text-slate-500 font-bold kelas-teks mt-0.5">NISN: <span class="text-blue-600 font-mono mr-2">${escapeHtml(s.nisn)}</span> KELAS: ${escapeHtml(kelasStr)}</p>
                </div>
            </label>
            `;
        });

        document.getElementById('containerDaftarSiswa').innerHTML = htmlList;
        toggleModal(mPlot, cPlot, true);
    }

    function tutupModalPlot() {
        toggleModal(mPlot, cPlot, false);
    }

    // 5. Fitur Realtime Search Filter
    let filterTimeout;

    function filterSiswa() {
        // Tampilkan Spinner (CSS Tailwind Conflict Fix)
        document.getElementById('loadingSpinner').classList.remove('hidden');
        document.getElementById('loadingSpinner').classList.add('flex');

        clearTimeout(filterTimeout);

        filterTimeout = setTimeout(() => {
            let keyword = document.getElementById('searchSiswa').value.toLowerCase();
            let items = document.querySelectorAll('.siswa-item');

            requestAnimationFrame(() => {
                items.forEach(item => {
                    let searchString = item.getAttribute('data-search');
                    item.style.display = searchString.includes(keyword) ? 'flex' : 'none';
                });

                // Hilangkan Spinner (CSS Tailwind Conflict Fix)
                document.getElementById('loadingSpinner').classList.add('hidden');
                document.getElementById('loadingSpinner').classList.remove('flex');
            });
        }, 300);
    }

    // 6. Fitur Toggle Centang Massal
    let isCheckedAll = false;

    function checkSemuaTerfilter() {
        isCheckedAll = !isCheckedAll;
        let items = document.querySelectorAll('.siswa-item');
        let count = 0;

        items.forEach(item => {
            if (item.style.display !== 'none') {
                item.querySelector('input[type="checkbox"]').checked = isCheckedAll;
                count++;
            }
        });

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: count + ' siswa di' + (isCheckedAll ? 'centang' : 'hapus centangnya'),
            showConfirmButton: false,
            timer: 1500
        });
    }

    // 7. Modul Modal List Detail Siswa
    const mListSiswa = document.getElementById('modalListSiswa');
    const cListSiswa = document.getElementById('modalListSiswaContent');

    function bukaModalListSiswa(ruanganId, namaRuangan) {
        document.getElementById('labelDetailRuangan').innerText = namaRuangan;
        let htmlList = '';
        let no = 1;

        dataSiswa.forEach(s => {
            if (s.ruangan_id == ruanganId) {
                let kelasStr = s.tingkat + ' ' + s.jurusan + ' ' + s.rombel;
                htmlList += `
                <div class="flex items-center p-3 mb-2 bg-white border border-slate-200 rounded-lg shadow-sm hover:border-blue-300 transition-colors">
                    <div class="w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-700 font-bold rounded-full text-xs mr-3 shrink-0">${no++}</div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-slate-800 uppercase tracking-wide">${escapeHtml(s.nama_lengkap)}</p>
                        <p class="text-[11px] text-slate-500 font-bold mt-0.5">NISN: <span class="text-blue-600 font-mono mr-2">${escapeHtml(s.nisn)}</span> KELAS: ${escapeHtml(kelasStr)}</p>
                    </div>
                </div>
                `;
            }
        });

        if (htmlList === '') htmlList = '<div class="text-center py-8 text-slate-400 font-medium">Data penghuni tidak ditemukan.</div>';

        document.getElementById('containerListSiswa').innerHTML = htmlList;
        toggleModal(mListSiswa, cListSiswa, true);
    }

    function tutupModalListSiswa() {
        toggleModal(mListSiswa, cListSiswa, false);
    }

    // 8. Modul Inline Edit Nama Ruangan
    function enableEditRuangan(id) {
        document.getElementById('view-ruangan-' + id).classList.add('hidden');
        document.getElementById('view-ruangan-' + id).classList.remove('flex');

        let form = document.getElementById('form-ruangan-' + id);
        form.classList.remove('hidden');
        form.classList.add('flex');

        let input = document.getElementById('input-ruangan-' + id);
        input.focus();
        let val = input.value;
        input.value = '';
        input.value = val; // Force kursor ke paling belakang text
    }

    function disableEditRuangan(id) {
        let form = document.getElementById('form-ruangan-' + id);
        form.classList.add('hidden');
        form.classList.remove('flex');

        document.getElementById('view-ruangan-' + id).classList.remove('hidden');
        document.getElementById('view-ruangan-' + id).classList.add('flex');
    }

    // Core Animation Modal
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
</script>
<?= $this->endSection() ?>