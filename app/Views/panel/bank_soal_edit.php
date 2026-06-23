<?php

/**
 * @var array $soal
 * @var array $mapel
 */
?>
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

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Edit Soal</h2>
        <p class="text-slate-500 text-sm mt-1">Mata Pelajaran: <strong class="text-blue-600 font-bold tracking-wide"><?= esc($mapel['nama_mapel']) ?></strong></p>
    </div>
    <a href="/panel/bank-soal?mapel=<?= $mapel['id'] ?>" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-sm flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Kembali ke Daftar
    </a>
</div>

<form action="/panel/bank-soal/update/<?= $soal['id'] ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative">
    <?= csrf_field() ?>
    <input type="hidden" name="mapel_id" value="<?= $mapel['id'] ?>">

    <div class="p-6 md:p-8 space-y-8">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-200 shadow-inner">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tipe Soal</label>
                <select name="jenis_soal" id="jenisSoalSelect" onchange="toggleJenisSoal()" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-slate-700 shadow-sm bg-white cursor-pointer transition">
                    <option value="pg" <?= $soal['jenis_soal'] == 'pg' ? 'selected' : '' ?>>Pilihan Ganda (PG)</option>
                    <option value="essai" <?= $soal['jenis_soal'] == 'essai' ? 'selected' : '' ?>>Soal Essai</option>
                </select>
            </div>
            <div>
                <label class="flex items-center text-sm font-semibold text-slate-700 mb-2">
                    <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                    </svg>
                    Upload Audio Listening (Opsional)
                </label>

                <?php if ($soal['file_audio']): ?>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-3 p-3 bg-white border border-blue-200 rounded-xl shadow-sm">
                        <audio controls controlsList="nodownload" class="h-9 w-full max-w-[240px] outline-none">
                            <source src="<?= base_url('uploads/audio/' . $soal['file_audio']) ?>" type="audio/mpeg">
                        </audio>
                        <div class="flex items-center gap-1.5 px-3 py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 transition cursor-pointer" onclick="document.getElementById('hapus_audio').click()">
                            <input type="checkbox" name="hapus_audio" value="1" id="hapus_audio" class="w-4 h-4 cursor-pointer text-red-600 focus:ring-red-500 rounded border-red-300">
                            <label for="hapus_audio" class="text-[11px] font-bold cursor-pointer tracking-wider">HAPUS AUDIO</label>
                        </div>
                    </div>
                <?php endif; ?>

                <input type="file" name="file_audio" accept=".mp3, audio/mpeg" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 cursor-pointer outline-none border border-slate-200 shadow-sm transition">
                <p class="text-[11px] mt-2 text-slate-500 font-medium">Hanya format MP3. Maks. 2MB. (Upload baru akan otomatis menimpa file lama)</p>
            </div>
        </div>

        <div>
            <label class="inline-flex items-center text-lg font-bold text-slate-800 mb-1 border-b-2 border-slate-800 pb-1">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Pertanyaan Utama
            </label>
            <p class="text-[11px] text-slate-500 mb-3 font-medium">Anda bisa Paste (Ctrl+V) gambar dari Snipping Tool/Screenshot langsung ke dalam editor di bawah.</p>
            <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <textarea name="pertanyaan" class="summernote" required><?= htmlspecialchars($soal['pertanyaan'] ?? '') ?></textarea>
            </div>
        </div>

        <div id="areaPG" class="<?= $soal['jenis_soal'] == 'essai' ? 'hidden' : '' ?> space-y-4 pt-6 border-t border-slate-200">
            <label class="inline-flex items-center text-lg font-bold text-slate-800 border-b-2 border-blue-600 pb-1">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                Pilihan Jawaban
            </label>
            <p class="text-[11px] text-slate-500 mb-4 font-medium">Klik radio button di sebelah kiri abjad untuk menentukan <strong class="text-emerald-600">Kunci Jawaban</strong>.</p>

            <?php foreach (['A', 'B', 'C', 'D', 'E'] as $abjad):
                $isiOpsi = $opsi[$abjad] ?? $opsi[strtolower($abjad)] ?? '';
            ?>
                <div class="flex flex-col md:flex-row gap-5 items-start bg-slate-50/50 p-5 border border-slate-200 rounded-2xl hover:border-blue-400 hover:bg-white transition-all shadow-sm">
                    <div class="flex md:flex-col items-center gap-3 mt-3">
                        <span class="font-black text-3xl text-slate-300 drop-shadow-sm"><?= $abjad ?></span>
                        <input type="radio" name="kunci_jawaban" value="<?= $abjad ?>" <?= ($kunciJawaban === $abjad) ? 'checked' : '' ?> class="w-6 h-6 text-emerald-600 cursor-pointer border-slate-300 focus:ring-emerald-500 shadow-sm transition">
                    </div>
                    <div class="flex-1 w-full border border-slate-200 rounded-xl overflow-hidden shadow-sm bg-white">
                        <textarea name="opsi_<?= strtolower($abjad) ?>" class="summernote-opsi"><?= htmlspecialchars($isiOpsi) ?></textarea>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="areaEssai" class="<?= $soal['jenis_soal'] == 'pg' ? 'hidden' : '' ?> space-y-2 pt-6 border-t border-slate-200">
            <label class="inline-flex items-center text-lg font-bold text-slate-800 border-b-2 border-amber-500 pb-1">
                <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Kunci Jawaban Essai / Referensi Penilaian
            </label>
            <p class="text-[11px] text-slate-500 mb-4 font-medium">Informasi ini hanya akan dilihat oleh Guru Pengampu saat proses koreksi manual.</p>
            <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <textarea name="kunci_essai" class="summernote-opsi"><?= $soal['jenis_soal'] == 'essai' ? htmlspecialchars($soal['kunci_jawaban'] ?? '') : '' ?></textarea>
            </div>
        </div>
    </div>

    <div class="px-6 py-5 bg-slate-50 border-t border-slate-200 flex flex-col-reverse sm:flex-row justify-end gap-3 sticky bottom-0 z-10 shadow-[0_-4px_15px_rgba(0,0,0,0.05)]">
        <a href="/panel/bank-soal?mapel=<?= $mapel['id'] ?>" class="px-6 py-3 border border-slate-300 bg-white rounded-xl text-slate-700 hover:bg-slate-100 font-bold text-center transition shadow-sm">Batalkan</a>
        <button type="submit" class="px-8 py-3 bg-amber-500 text-white rounded-xl hover:bg-amber-600 font-bold shadow-lg shadow-amber-500/30 transition flex items-center justify-center transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
            </svg>
            Simpan Perubahan
        </button>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let csrfTokenName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';

    $(document).ready(function() {
        const summernoteConfig = {
            toolbar: [
                ['font', ['bold', 'italic', 'underline', 'superscript', 'subscript', 'clear']],
                ['insert', ['picture', 'table']],
                ['view', ['fullscreen', 'codeview']],
            ],
            callbacks: {
                onImageUpload: function(files) {
                    if (typeof showToast === 'function') {
                        showToast("Sedang memproses dan mengunggah gambar...", "success");
                    }

                    for (let i = 0; i < files.length; i++) {
                        compressAndUploadImage(files[i], $(this));
                    }
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

    function compressAndUploadImage(file, editorElement) {
        if (!file.type.match(/image.*/)) {
            if (typeof showToast === 'function') showToast("Bukan file gambar!", "error");
            else alert("Bukan file gambar!");
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

                canvas.toBlob(function(blob) {
                    let formData = new window.FormData();
                    formData.append('gambar_soal', blob, 'img_' + Date.now() + '.webp');
                    formData.append(csrfTokenName, csrfHash);

                    $.ajax({
                        url: '/panel/bank-soal/upload-gambar',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                editorElement.summernote('insertImage', response.url);

                                // FIX: Mencegah error 403 saat mensubmit form dengan token yang sudah di-refresh
                                csrfHash = response.csrf;
                                $('input[name="' + csrfTokenName + '"]').val(csrfHash);
                            } else {
                                if (typeof showToast === 'function') showToast(response.message, "error");
                                else alert(response.message);
                            }
                        },
                        error: function() {
                            if (typeof showToast === 'function') showToast("Gagal terhubung ke server saat unggah gambar.", "error");
                            else alert("Gagal terhubung ke server.");
                        }
                    });
                }, 'image/webp', 0.8);
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