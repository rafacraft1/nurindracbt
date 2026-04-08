<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<?php
$kunciJawaban = trim(strtoupper($soal['kunci_jawaban'] ?? ''));

$opsi = [];
if (!empty($soal['opsi_jawaban'])) {
    $decoded = json_decode($soal['opsi_jawaban'], true);

    if (is_string($decoded)) {
        $decoded = json_decode($decoded, true);
    }

    if (is_array($decoded)) {
        $opsi = $decoded;
    }
}
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Edit Soal</h2>
        <p class="text-slate-500 text-sm mt-1">Mata Pelajaran: <strong class="text-blue-600"><?= esc($mapel['nama_mapel']) ?></strong></p>
    </div>
    <a href="/panel/bank-soal?mapel=<?= $mapel['id'] ?>" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded-lg font-medium text-sm transition shadow-sm">
        Kembali ke Daftar
    </a>
</div>

<form action="/panel/bank-soal/update/<?= $soal['id'] ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden relative">
    <?= csrf_field() ?>
    <input type="hidden" name="mapel_id" value="<?= $mapel['id'] ?>">

    <div class="p-6 md:p-8 space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-5 rounded-xl border border-slate-200">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tipe Soal</label>
                <select name="jenis_soal" id="jenisSoalSelect" onchange="toggleJenisSoal()" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-slate-700 shadow-sm bg-white">
                    <option value="pg" <?= $soal['jenis_soal'] == 'pg' ? 'selected' : '' ?>>Pilihan Ganda (PG)</option>
                    <option value="essai" <?= $soal['jenis_soal'] == 'essai' ? 'selected' : '' ?>>Soal Essai</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Upload Audio Listening (Opsional)</label>

                <?php if ($soal['file_audio']): ?>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-3 p-2 bg-white border border-blue-200 rounded-lg shadow-sm">
                        <audio controls controlsList="nodownload" class="h-8 w-full max-w-[220px]">
                            <source src="<?= base_url('uploads/audio/' . $soal['file_audio']) ?>" type="audio/mpeg">
                        </audio>
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-md">
                            <input type="checkbox" name="hapus_audio" value="1" id="hapus_audio" class="w-4 h-4 cursor-pointer text-red-600 focus:ring-red-500">
                            <label for="hapus_audio" class="text-xs font-bold cursor-pointer tracking-wider">HAPUS AUDIO</label>
                        </div>
                    </div>
                <?php endif; ?>

                <input type="file" name="file_audio" accept=".mp3, audio/mpeg" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 cursor-pointer outline-none">
                <p class="text-[10px] mt-1 text-slate-500">Hanya format MP3. Maks. 2MB. (Upload baru akan otomatis menimpa file lama)</p>
            </div>
        </div>

        <div>
            <label class="inline-block text-base font-bold text-slate-800 mb-2 border-b-2 border-slate-800 pb-1">Pertanyaan Utama</label>
            <p class="text-xs text-slate-500 mb-3">Anda bisa Paste (Ctrl+V) gambar dari Snipping Tool/Screenshot langsung ke dalam editor di bawah.</p>
            <textarea name="pertanyaan" class="summernote" required><?= htmlspecialchars($soal['pertanyaan'] ?? '') ?></textarea>
        </div>

        <div id="areaPG" class="<?= $soal['jenis_soal'] == 'essai' ? 'hidden' : '' ?> space-y-4 pt-6 border-t border-slate-200">
            <label class="inline-block text-base font-bold text-slate-800 border-b-2 border-blue-600 pb-1">Pilihan Jawaban</label>
            <p class="text-xs text-slate-500 mb-2">Klik radio button di sebelah kiri abjad untuk menentukan <strong class="text-emerald-600">Kunci Jawaban</strong>.</p>

            <?php foreach (['A', 'B', 'C', 'D', 'E'] as $abjad):
                $isiOpsi = $opsi[$abjad] ?? $opsi[strtolower($abjad)] ?? '';
            ?>
                <div class="flex flex-col md:flex-row gap-4 items-start bg-white p-4 border border-slate-200 rounded-xl hover:border-blue-400 hover:shadow-md transition">
                    <div class="flex md:flex-col items-center gap-3 mt-2">
                        <span class="font-black text-2xl text-slate-400"><?= $abjad ?></span>
                        <input type="radio" name="kunci_jawaban" value="<?= $abjad ?>" <?= ($kunciJawaban === $abjad) ? 'checked' : '' ?> class="w-6 h-6 text-emerald-600 cursor-pointer border-slate-300 focus:ring-emerald-500 shadow-sm">
                    </div>
                    <div class="flex-1 w-full">
                        <textarea name="opsi_<?= strtolower($abjad) ?>" class="summernote-opsi"><?= htmlspecialchars($isiOpsi) ?></textarea>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="areaEssai" class="<?= $soal['jenis_soal'] == 'pg' ? 'hidden' : '' ?> space-y-2 pt-6 border-t border-slate-200">
            <label class="inline-block text-base font-bold text-slate-800 border-b-2 border-amber-600 pb-1">Kunci Jawaban Essai / Referensi Penilaian</label>
            <p class="text-xs text-slate-500 mb-3">Informasi ini hanya akan dilihat oleh Guru Pengampu saat proses koreksi manual.</p>
            <textarea name="kunci_essai" class="summernote-opsi"><?= $soal['jenis_soal'] == 'essai' ? htmlspecialchars($soal['kunci_jawaban'] ?? '') : '' ?></textarea>
        </div>
    </div>

    <div class="px-6 py-5 bg-slate-50 border-t border-slate-200 flex flex-col-reverse sm:flex-row justify-end gap-3 sticky bottom-0 z-10 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <a href="/panel/bank-soal?mapel=<?= $mapel['id'] ?>" class="px-6 py-2.5 border border-slate-300 bg-white rounded-lg text-slate-700 hover:bg-slate-50 font-bold text-center transition shadow-sm">Batalkan</a>
        <button type="submit" class="px-8 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-lg shadow-blue-500/30 transition text-lg flex items-center justify-center">
            💾 Simpan Perubahan
        </button>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        const summernoteConfig = {
            toolbar: [
                ['font', ['bold', 'italic', 'underline', 'superscript', 'subscript', 'clear']],
                ['insert', ['picture', 'table']],
                ['view', ['fullscreen', 'codeview']],
            ],
            callbacks: {
                onImageUpload: function(files) {
                    for (let i = 0; i < files.length; i++) compressAndInsertImage(files[i], $(this));
                }
            }
        };

        $('.summernote').summernote({
            ...summernoteConfig,
            height: 250
        });
        $('.summernote-opsi').summernote({
            ...summernoteConfig,
            height: 100
        });

        toggleJenisSoal();
    });

    function compressAndInsertImage(file, editorElement) {
        if (!file.type.match(/image.*/)) {
            showToast("Bukan file gambar!", "error");
            return;
        }
        const reader = new window.FileReader();
        reader.onload = function(e) {
            const image = new window.Image();
            image.onload = function() {
                const canvas = document.createElement('canvas');
                let width = image.width,
                    height = image.height,
                    MAX = 800;
                if (width > MAX) {
                    height *= MAX / width;
                    width = MAX;
                }
                canvas.width = width;
                canvas.height = height;
                canvas.getContext('2d').drawImage(image, 0, 0, width, height);
                editorElement.summernote('insertImage', canvas.toDataURL('image/webp', 0.8));
            }
            image.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }

    const areaPG = document.getElementById('areaPG');
    const areaEssai = document.getElementById('areaEssai');

    function toggleJenisSoal() {
        const jenis = document.getElementById('jenisSoalSelect').value;
        const radioKunci = document.querySelectorAll('input[name="kunci_jawaban"]');

        if (jenis === 'pg') {
            areaPG.classList.remove('hidden');
            areaEssai.classList.add('hidden');
            if (radioKunci.length > 0) radioKunci[0].required = true;
        } else {
            areaPG.classList.add('hidden');
            areaEssai.classList.remove('hidden');
            if (radioKunci.length > 0) radioKunci[0].required = false;
        }
    }
</script>
<?= $this->endSection() ?>