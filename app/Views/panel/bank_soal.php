<?php

/**
 * @var array $mapel
 * @var string|int $filterMapelId
 * @var array $soal
 */
?>
<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 border-b border-slate-200 pb-5">
    <div>
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Bank Soal Ujian</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola soal, tambah manual, atau import via Excel secara efisien.</p>
    </div>

    <form action="/panel/bank-soal" method="GET" class="w-full lg:w-auto" id="filterForm">
        <select name="mapel" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white shadow-sm outline-none text-sm w-full lg:w-max font-bold text-slate-700 transition cursor-pointer">
            <?php if (empty($mapel)): ?>
                <option value="">-- Anda belum diplot ke mapel apapun --</option>
            <?php endif; ?>
            <?php foreach ($mapel as $m): ?>
                <option value="<?= $m['id'] ?>" <?= $filterMapelId == $m['id'] ? 'selected' : '' ?>>
                    <?= esc($m['nama_mapel']) ?> (PG: <?= $m['total_pg'] ?? 0 ?> | Essai: <?= $m['total_essai'] ?? 0 ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<?php if ($filterMapelId): ?>
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="/panel/bank-soal/create?mapel=<?= $filterMapelId ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-lg shadow-blue-500/30 flex items-center transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Soal Manual
        </a>
        <button onclick="bukaModalImport()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-lg shadow-emerald-500/30 flex items-center transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
            Import Excel
        </button>
        <a href="/panel/bank-soal/export/<?= $filterMapelId ?>" class="bg-slate-700 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download Template
        </a>
    </div>
<?php endif; ?>

<div class="space-y-4">
    <?php if (empty($soal) && !empty($mapel)): ?>
        <div class="bg-white p-12 rounded-2xl shadow-sm border border-slate-200 text-center text-slate-500 border-dashed flex flex-col items-center justify-center">
            <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="font-bold text-slate-600 text-lg">Belum ada soal terdaftar.</p>
            <p class="text-sm mt-1">Buat secara manual atau download template Excel lalu import.</p>
        </div>
    <?php endif; ?>

    <?php $no = 1;
    foreach ($soal as $s): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative group">
            <div class="absolute top-0 right-0 px-3 py-1 rounded-bl-xl text-[10px] font-bold text-white <?= $s['jenis_soal'] == 'pg' ? 'bg-blue-600' : 'bg-emerald-600' ?> uppercase tracking-wider shadow-sm z-10">
                <?= $s['jenis_soal'] == 'pg' ? 'Pilihan Ganda' : 'Essai' ?>
            </div>

            <div class="p-6 flex gap-5 items-start">
                <div class="w-10 h-10 shrink-0 bg-slate-50 rounded-xl flex items-center justify-center font-bold text-slate-500 border border-slate-200 shadow-inner text-lg">
                    <?= $no++ ?>
                </div>

                <div class="flex-1 w-full overflow-hidden">
                    <div class="prose prose-sm max-w-none text-slate-800 mb-4 font-medium leading-relaxed">
                        <?= $s['pertanyaan'] ?>
                    </div>

                    <?php if ($s['file_audio']): ?>
                        <div class="mb-5 p-3.5 bg-indigo-50 border border-indigo-100 rounded-xl inline-block shadow-sm">
                            <p class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider mb-2 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                                </svg>
                                Audio Listening
                            </p>
                            <audio controls controlsList="nodownload" class="h-9 w-64 md:w-80 outline-none rounded-lg shadow-sm">
                                <source src="<?= base_url('uploads/audio/' . $s['file_audio']) ?>" type="audio/mpeg">
                            </audio>
                        </div>
                    <?php endif; ?>

                    <?php if ($s['jenis_soal'] == 'pg'):
                        $opsi = json_decode($s['opsi_jawaban'], true);
                    ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2">
                            <?php foreach (['A', 'B', 'C', 'D', 'E'] as $abjad): if (isset($opsi[$abjad]) && trim($opsi[$abjad]) !== ''): ?>
                                    <div class="flex p-3 rounded-xl border transition-colors <?= $s['kunci_jawaban'] == $abjad ? 'bg-emerald-50 border-emerald-300 ring-1 ring-emerald-500 shadow-sm' : 'bg-slate-50 border-slate-200' ?>">
                                        <span class="font-black text-lg mr-3 mt-0.5 <?= $s['kunci_jawaban'] == $abjad ? 'text-emerald-700' : 'text-slate-400' ?>"><?= $abjad ?>.</span>
                                        <div class="text-sm text-slate-700 prose prose-sm max-w-none mt-1"><?= $opsi[$abjad] ?></div>
                                    </div>
                            <?php endif;
                            endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl shadow-sm">
                            <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider mb-2 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                                Kunci / Referensi Penilaian Essai:
                            </span>
                            <div class="text-sm text-amber-900 prose prose-sm max-w-none font-medium"><?= $s['kunci_jawaban'] ?: '<i>Tidak ada referensi tertulis.</i>' ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="shrink-0 ml-2 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <a href="/panel/bank-soal/edit/<?= $s['id'] ?>" class="p-2.5 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-xl transition border border-amber-200 shadow-sm flex items-center justify-center" title="Edit Soal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </a>

                    <form action="/panel/bank-soal/delete/<?= $s['id'] ?>" method="POST" id="formDelete<?= $s['id'] ?>">
                        <?= csrf_field() ?>
                        <button type="button" onclick="konfirmasiHapus(<?= $s['id'] ?>)" class="p-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition border border-red-200 shadow-sm flex items-center justify-center w-full" title="Hapus Soal">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>

            </div>
            <div class="bg-slate-50 px-6 py-2.5 border-t border-slate-200 text-[11px] text-slate-500 flex justify-between font-medium tracking-wide">
                <span class="flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Dibuat oleh: <strong class="ml-1 text-slate-700"><?= esc($s['nama_guru']) ?></strong>
                </span>
                <span class="font-mono">ID: #<?= $s['id'] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div id="modalImport" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform" id="modalImportContent">
        <div class="bg-emerald-600 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Import Soal Excel
            </h3>
            <button type="button" onclick="tutupModalImport()" class="text-emerald-100 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="/panel/bank-soal/import" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="mapel_id" value="<?= $filterMapelId ?>">

            <div class="p-6 space-y-5">
                <div class="bg-amber-50 border border-amber-200 p-4 rounded-xl text-xs text-amber-800 leading-relaxed shadow-inner">
                    <strong class="flex items-center mb-1">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Catatan Penting:
                    </strong>
                    Untuk memasukkan Gambar atau File Audio, wajib menggunakan <b>Form Buat Soal Manual</b> atau edit soal setelah di-import. Import Excel murni hanya untuk meretur teks dasar dengan cepat.
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Upload File (.xlsx)</label>
                    <input type="file" name="file_excel" accept=".xlsx, .xls" required class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-slate-200 shadow-sm transition">
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="tutupModalImport()" class="px-5 py-2.5 border border-slate-300 bg-white rounded-xl text-slate-700 hover:bg-slate-50 font-bold transition shadow-sm">Batal</button>
                <button type="submit" onclick="this.innerHTML='<svg class=\'animate-spin w-4 h-4 mr-2\' xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg> Memproses...'" class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-bold shadow-md shadow-emerald-500/30 transition flex items-center">
                    Upload & Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const modalImport = document.getElementById('modalImport');
    const modalImportContent = document.getElementById('modalImportContent');

    function bukaModalImport() {
        toggleModal(modalImport, modalImportContent, true);
    }

    function tutupModalImport() {
        toggleModal(modalImport, modalImportContent, false);
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
            title: 'Hapus Soal?',
            text: "Data soal beserta file audio (jika ada) akan dihapus secara permanen.",
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