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
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-4 text-center font-medium text-slate-500"><?= $no++ ?></td>
                        <td class="px-4 py-4 font-bold text-slate-800 text-base uppercase">
                            <?= esc($r['nama_ruangan']) ?>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <?php if ($r['jumlah_siswa'] > 0): ?>
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 font-bold rounded-full text-xs shadow-sm">
                                    👥 <?= $r['jumlah_siswa'] ?> Siswa
                                </span>
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
                    <input type="text" id="searchSiswa" onkeyup="filterSiswa()" placeholder="Ketik Kelas (Misal: XII RPL 1) atau Nama..." class="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none shadow-sm">
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

    // 3. Modul Dynamic Plot Siswa (Zero-Lag Engine)
    const mPlot = document.getElementById('modalPlot');
    const cPlot = document.getElementById('modalPlotContent');
    const dataSiswa = <?= json_encode($siswa) ?>; // Menyedot data array PHP langsung ke JavaScript!

    function bukaModalPlot(ruanganId, namaRuangan) {
        document.getElementById('inputPlotRuanganId').value = ruanganId;
        document.getElementById('labelNamaRuangan').innerText = namaRuangan;
        document.getElementById('searchSiswa').value = ''; // Reset Pencarian

        let htmlList = '';

        dataSiswa.forEach(s => {
            // Cek apakah siswa saat ini sudah menjadi penghuni ruangan ini
            let isChecked = (s.ruangan_id == ruanganId) ? 'checked' : '';

            // Badge Keterangan Status Ruangan Asal
            let badge = '';
            if (s.ruangan_id && s.ruangan_id != ruanganId) {
                badge = '<span class="text-[9px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded ml-2 font-bold border border-red-200">Di Ruangan Lain</span>';
            } else if (s.ruangan_id == ruanganId) {
                badge = '<span class="text-[9px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded ml-2 font-bold border border-emerald-200">Sudah Masuk</span>';
            }

            let kelasStr = s.tingkat + ' ' + s.jurusan + ' ' + s.rombel;

            htmlList += `
            <label class="siswa-item flex items-center p-3 mb-1 bg-white border border-slate-200 rounded-lg hover:border-emerald-400 hover:bg-emerald-50 cursor-pointer transition-colors shadow-sm">
                <input type="checkbox" name="siswa_ids[]" value="${s.id}" ${isChecked} class="w-5 h-5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                <div class="ml-4 flex-1">
                    <p class="text-sm font-bold text-slate-800 uppercase tracking-wide nama-teks">${s.nama_lengkap} ${badge}</p>
                    <p class="text-[11px] text-slate-500 font-bold kelas-teks mt-0.5">NISN: <span class="text-blue-600 font-mono mr-2">${s.nisn}</span> KELAS: ${kelasStr}</p>
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

    // 4. Fitur Realtime Search Filter (Super Cepat!)
    function filterSiswa() {
        let keyword = document.getElementById('searchSiswa').value.toLowerCase();
        let items = document.querySelectorAll('.siswa-item');

        items.forEach(item => {
            let nama = item.querySelector('.nama-teks').innerText.toLowerCase();
            let kelas = item.querySelector('.kelas-teks').innerText.toLowerCase();

            if (nama.includes(keyword) || kelas.includes(keyword)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // 5. Fitur Centang Massal Kelas (Hanya yg Terlihat di Layar)
    function checkSemuaTerfilter() {
        let items = document.querySelectorAll('.siswa-item');
        items.forEach(item => {
            if (item.style.display !== 'none') {
                item.querySelector('input[type="checkbox"]').checked = true;
            }
        });
        showToast("Semua siswa yang tampil berhasil dicentang!", "success");
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