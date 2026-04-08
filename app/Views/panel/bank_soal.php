<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 border-b border-slate-200 pb-5">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Bank Soal Ujian</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola soal, tambah manual, atau import via Excel.</p>
    </div>

    <form action="/panel/bank-soal" method="GET" class="w-full lg:w-auto" id="filterForm">
        <select name="mapel" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white shadow-sm outline-none text-sm w-full lg:w-max font-semibold text-slate-700">
            <?php if (empty($mapel)): ?>
                <option value="">-- Anda belum diplot ke mapel apapun --</option>
            <?php endif; ?>
            <?php foreach ($mapel as $m): ?>
                <option value="<?= $m['id'] ?>" <?= $filterMapelId == $m['id'] ? 'selected' : '' ?>>
                    📚 <?= esc($m['nama_mapel']) ?> (PG: <?= $m['total_pg'] ?? 0 ?> | Essai: <?= $m['total_essai'] ?? 0 ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<?php if ($filterMapelId): ?>
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="/panel/bank-soal/create?mapel=<?= $filterMapelId ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow flex items-center">
            <span class="mr-2">➕</span> Buat Soal Manual
        </a>
        <button onclick="bukaModalImport()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow flex items-center">
            <span class="mr-2">📁</span> Import Excel
        </button>
        <a href="/panel/bank-soal/export/<?= $filterMapelId ?>" class="bg-slate-700 hover:bg-slate-800 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow flex items-center">
            <span class="mr-2">⬇️</span> Download / Template Excel
        </a>
    </div>
<?php endif; ?>

<div class="space-y-4">
    <?php if (empty($soal) && !empty($mapel)): ?>
        <div class="bg-white p-12 rounded-xl shadow-sm border border-slate-200 text-center text-slate-500 border-dashed">
            <span class="text-4xl block mb-3">📄</span>
            Belum ada soal terdaftar. Buat secara manual atau download template Excel lalu import.
        </div>
    <?php endif; ?>

    <?php $no = 1;
    foreach ($soal as $s): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden relative">
            <div class="absolute top-0 right-0 px-3 py-1 rounded-bl-lg text-[10px] font-bold text-white <?= $s['jenis_soal'] == 'pg' ? 'bg-blue-600' : 'bg-emerald-600' ?> uppercase tracking-wider shadow">
                <?= $s['jenis_soal'] == 'pg' ? 'Pilihan Ganda' : 'Essai' ?>
            </div>

            <div class="p-5 flex gap-4 items-start">
                <div class="w-8 h-8 shrink-0 bg-slate-100 rounded-full flex items-center justify-center font-bold text-slate-500 border border-slate-200">
                    <?= $no++ ?>
                </div>

                <div class="flex-1 w-full overflow-hidden">
                    <div class="prose prose-sm max-w-none text-slate-800 mb-3">
                        <?= $s['pertanyaan'] ?>
                    </div>

                    <?php if ($s['file_audio']): ?>
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-100 rounded-lg inline-block">
                            <p class="text-[10px] font-bold text-blue-700 uppercase mb-1">🎧 Audio Listening</p>
                            <audio controls controlsList="nodownload" class="h-8 w-64">
                                <source src="<?= base_url('uploads/audio/' . $s['file_audio']) ?>" type="audio/mpeg">
                            </audio>
                        </div>
                    <?php endif; ?>

                    <?php if ($s['jenis_soal'] == 'pg'):
                        $opsi = json_decode($s['opsi_jawaban'], true);
                    ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                            <?php foreach (['A', 'B', 'C', 'D', 'E'] as $abjad): if (isset($opsi[$abjad]) && trim($opsi[$abjad]) !== ''): ?>
                                    <div class="flex p-2 rounded border <?= $s['kunci_jawaban'] == $abjad ? 'bg-emerald-50 border-emerald-300 ring-1 ring-emerald-500' : 'bg-slate-50 border-slate-200' ?>">
                                        <span class="font-bold mr-3 <?= $s['kunci_jawaban'] == $abjad ? 'text-emerald-700' : 'text-slate-500' ?>"><?= $abjad ?>.</span>
                                        <div class="text-sm text-slate-700 prose prose-sm max-w-none"><?= $opsi[$abjad] ?></div>
                                    </div>
                            <?php endif;
                            endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                            <span class="text-[10px] font-bold text-amber-700 uppercase mb-1 block">📝 Kunci / Referensi Penilaian Essai:</span>
                            <div class="text-sm text-amber-900 prose prose-sm max-w-none"><?= $s['kunci_jawaban'] ?: '<i>Tidak ada referensi tertulis.</i>' ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="shrink-0 ml-2 flex flex-col gap-2">
                    <a href="/panel/bank-soal/edit/<?= $s['id'] ?>" class="p-2 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-md transition border border-amber-200 shadow-sm flex items-center justify-center" title="Edit Soal">
                        ✏️
                    </a>

                    <form action="/panel/bank-soal/delete/<?= $s['id'] ?>" method="POST" id="formDelete<?= $s['id'] ?>">
                        <?= csrf_field() ?>
                        <button type="button" onclick="konfirmasiHapus(<?= $s['id'] ?>)" class="p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-md transition border border-red-200 shadow-sm flex items-center justify-center w-full" title="Hapus Soal">
                            🗑️
                        </button>
                    </form>
                </div>

            </div>
            <div class="bg-slate-50 px-5 py-2 border-t border-slate-200 text-[10px] text-slate-400 flex justify-between">
                <span>Dibuat oleh: <strong><?= esc($s['nama_guru']) ?></strong></span>
                <span>ID Soal: #<?= $s['id'] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div id="modalImport" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform" id="modalImportContent">
        <div class="bg-emerald-600 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white">Import Soal Excel</h3>
            <button type="button" onclick="tutupModalImport()" class="text-emerald-100 hover:text-white">✖</button>
        </div>

        <form action="/panel/bank-soal/import" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="mapel_id" value="<?= $filterMapelId ?>">

            <div class="p-6 space-y-4">
                <div class="bg-amber-50 border border-amber-200 p-3 rounded-lg text-xs text-amber-800 leading-relaxed">
                    <strong>Catatan Penting:</strong><br>
                    Untuk memasukkan Gambar atau File Audio, wajib menggunakan <b>Form Buat Soal Manual</b> atau edit soal setelah di-import. Import Excel hanya untuk mengunggah teks dasar dengan cepat.
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Upload File (.xlsx)</label>
                    <input type="file" name="file_excel" accept=".xlsx, .xls" required class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t flex justify-end gap-3">
                <button type="button" onclick="tutupModalImport()" class="px-4 py-2 border rounded-lg text-slate-700 hover:bg-slate-100">Batal</button>
                <button type="submit" onclick="this.innerHTML='Memproses...'" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium shadow transition">Upload & Simpan</button>
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