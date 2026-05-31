<?php

/**
 * @var array $jenis_ujian
 */
?>
<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-200 pb-5">
    <div>
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Master Jenis Ujian</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola daftar jenis ujian seperti Penilaian Harian, PTS, PAS, dll.</p>
    </div>

    <button onclick="bukaModalJenisUjian()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-blue-600/20 flex items-center justify-center w-full md:w-auto transform hover:-translate-y-0.5">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Jenis Ujian
    </button>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden max-w-4xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 w-16 text-center">No</th>
                    <th class="px-6 py-4">Nama Jenis Ujian</th>
                    <th class="px-6 py-4 text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $no = 1;
                foreach ($jenis_ujian as $ju): ?>
                    <tr class="hover:bg-blue-50/30 transition-colors h-16">
                        <td class="px-6 py-4 text-center font-medium"><?= $no++ ?></td>

                        <td class="px-6 py-4 font-bold text-slate-800 uppercase">
                            <?= esc($ju['nama_ujian']) ?>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="editJenisUjian(<?= htmlspecialchars(json_encode($ju), ENT_QUOTES, 'UTF-8') ?>)" class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-200 rounded-lg transition-colors shadow-sm" title="Edit Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>

                                <form action="/panel/jenis-ujian/delete/<?= $ju['id'] ?>" method="POST" id="formDelete<?= $ju['id'] ?>" class="inline-block">
                                    <?= csrf_field() ?>
                                    <button type="button" onclick="konfirmasiHapus(<?= $ju['id'] ?>)" class="p-2 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-colors shadow-sm" title="Hapus Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($jenis_ujian)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-slate-500 bg-slate-50">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Belum ada data jenis ujian.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalJenisUjian" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform" id="modalJenisUjianContent">
        <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white flex items-center gap-2" id="modalTitle">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Tambah Jenis Ujian
            </h3>
            <button type="button" onclick="tutupModalJenisUjian()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="/panel/jenis-ujian/store" method="POST" id="formJenisUjian">
            <?= csrf_field() ?>
            <div class="p-6">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Jenis Ujian</label>
                <input type="text" name="nama_ujian" id="inputNamaUjian" placeholder="Contoh: PENILAIAN TENGAH SEMESTER" required class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none uppercase font-bold text-slate-700 shadow-sm transition">
                <p class="text-[11px] text-slate-500 mt-2 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Nama ini akan muncul pada Kartu Ujian dan Laporan Nilai.
                </p>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="tutupModalJenisUjian()" class="px-5 py-2.5 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-100 font-semibold transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold shadow-md shadow-blue-600/30 transition flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const mJU = document.getElementById('modalJenisUjian');
    const cJU = document.getElementById('modalJenisUjianContent');
    const fJU = document.getElementById('formJenisUjian');

    function bukaModalJenisUjian() {
        document.getElementById('modalTitle').innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg> Tambah Jenis Ujian';
        fJU.action = '/panel/jenis-ujian/store';
        fJU.reset();
        toggleModal(mJU, cJU, true);
    }

    function editJenisUjian(data) {
        document.getElementById('modalTitle').innerHTML = '<svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg> Edit Jenis Ujian';
        fJU.action = '/panel/jenis-ujian/update/' + data.id;
        document.getElementById('inputNamaUjian').value = data.nama_ujian;
        toggleModal(mJU, cJU, true);
    }

    function tutupModalJenisUjian() {
        toggleModal(mJU, cJU, false);
    }

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

    function konfirmasiHapus(id) {
        Swal.fire({
            title: 'Hapus Jenis Ujian?',
            text: "Pastikan data ini tidak sedang dipakai di Jadwal Ujian manapun.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('formDelete' + id).submit();
        });
    }
</script>
<?= $this->endSection() ?>