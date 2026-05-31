<?php

/**
 * @var array $jadwal
 * @var array $siswa
 * @var array $soal_essai
 * @var array $jawaban_json
 * @var array $hasil
 * @var string|null $rombelFilter
 */

$urlKembali = "/panel/penilaian/detail/" . $jadwal['id'];
if (!empty($rombelFilter)) {
    $urlKembali .= "?rombel=" . urlencode($rombelFilter);
}
?>
<?= $this->extend('layouts/panel') ?>
<?= $this->section('content') ?>

<div class="mb-6 border-b border-slate-200 pb-5">
    <a href="<?= $urlKembali ?>" class="text-blue-600 hover:text-blue-800 text-sm font-bold mb-3 flex items-center group transition">
        <svg class="w-4 h-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Kembali ke Data Kelas
    </a>
    <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Koreksi Jawaban Essai</h2>

    <div class="flex items-center gap-2 mt-2">
        <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Siswa:</span>
        <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg text-sm font-black uppercase tracking-wide border border-blue-100">
            <?= esc($siswa['nama_lengkap']) ?>
        </span>
        <span class="text-xs font-mono font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded border border-slate-200 ml-1">
            NISN: <?= esc($siswa['nisn']) ?>
        </span>
    </div>
</div>

<div class="space-y-6 pb-28">
    <?php if (empty($soal_essai)): ?>
        <div class="bg-white py-16 px-6 text-center rounded-2xl border-2 border-dashed border-slate-300">
            <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-slate-600 font-bold text-lg">Tidak ada soal essai pada mata pelajaran ini.</p>
        </div>
    <?php else: ?>
        <?php $no = 1;
        foreach ($soal_essai as $soal):
            $idSoal = $soal['id'];
            $jawabanSiswa = $jawaban_json[$idSoal]['jawab'] ?? '<span class="text-red-500 italic font-medium flex items-center"><svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Siswa tidak mengisi jawaban (Kosong)</span>';
        ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-slate-200"></div>

                <div class="bg-slate-50 border-b border-slate-200 px-6 py-3 font-black text-slate-700 flex items-center">
                    <span class="bg-slate-800 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs mr-3 shadow-sm"><?= $no++ ?></span>
                    Pertanyaan Essai
                </div>

                <div class="p-6">
                    <div class="prose max-w-none text-sm text-slate-800 mb-6 p-5 bg-slate-50 rounded-xl border border-slate-200 shadow-inner">
                        <?= $soal['pertanyaan'] ?>

                        <?php if ($soal['kunci_jawaban']): ?>
                            <div class="mt-4 pt-4 border-t border-slate-200 text-xs text-emerald-800 font-medium">
                                <span class="inline-flex items-center bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded border border-emerald-200 font-bold uppercase tracking-wider mb-2">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Referensi Jawaban Guru:
                                </span>
                                <div class="prose prose-sm max-w-none"><?= $soal['kunci_jawaban'] ?></div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <label class="flex items-center text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                        <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Jawaban Siswa:
                    </label>
                    <div class="w-full p-5 bg-blue-50/50 border border-blue-200 rounded-xl text-slate-800 font-medium whitespace-pre-wrap leading-relaxed shadow-sm min-h-[100px]">
                        <?= $jawabanSiswa ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="fixed bottom-0 right-0 w-full lg:w-[calc(100%-16rem)] bg-white border-t border-slate-200 shadow-[0_-10px_20px_-5px_rgba(0,0,0,0.08)] p-5 z-40">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-5">
        <div class="flex items-center gap-4 bg-slate-50 px-5 py-3 rounded-xl border border-slate-200">
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Skor Pilihan Ganda:</p>
                <p class="text-3xl font-black text-blue-600 drop-shadow-sm"><?= number_format($hasil['nilai_pg'] ?? 0, 1) ?></p>
            </div>
        </div>

        <form action="/panel/penilaian/simpan-koreksi" method="POST" class="flex flex-col sm:flex-row items-end sm:items-center gap-4 w-full sm:w-auto">
            <?= csrf_field() ?>
            <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
            <input type="hidden" name="siswa_id" value="<?= $siswa['id'] ?>">
            <input type="hidden" name="rombel" value="<?= esc($rombelFilter ?? '') ?>">

            <div class="w-full sm:w-auto">
                <label class="text-[10px] font-bold text-indigo-600 uppercase block mb-1.5 tracking-wider">Input Skor Essai (0-100)</label>
                <div class="relative">
                    <input type="number" name="nilai_essai" value="<?= $hasil['nilai_essai'] ?? 0 ?>" min="0" max="100" step="0.1" required class="w-full sm:w-40 pl-4 pr-10 py-3 border-2 border-indigo-300 rounded-xl text-xl font-black text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-inner transition text-center bg-indigo-50/30">
                    <span class="absolute right-4 top-4 text-xs font-bold text-indigo-400">/100</span>
                </div>
            </div>

            <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition flex items-center justify-center transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Simpan Penilaian
            </button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>