<?= $this->extend('layouts/panel') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <a href="/panel/penilaian/detail/<?= $jadwal['id'] ?>" class="text-blue-600 hover:underline text-sm font-semibold mb-2 inline-block">← Kembali ke Data Kelas</a>
    <h2 class="text-2xl font-bold text-slate-800">Koreksi Jawaban Essai</h2>
    <p class="text-slate-600 font-semibold mt-1">Siswa: <span class="uppercase text-blue-600"><?= esc($siswa['nama_lengkap']) ?></span> | NISN: <?= esc($siswa['nisn']) ?></p>
</div>

<div class="space-y-6 pb-24">
    <?php if (empty($soal_essai)): ?>
        <div class="bg-white p-8 text-center rounded-xl border border-slate-200">
            <p class="text-slate-500 font-bold">Tidak ada soal essai pada mata pelajaran ini.</p>
        </div>
    <?php else: ?>
        <?php $no = 1;
        foreach ($soal_essai as $soal):
            $idSoal = $soal['id'];
            $jawabanSiswa = $jawaban_json[$idSoal]['jawab'] ?? '<span class="text-red-500 italic">Tidak dijawab (Kosong)</span>';
        ?>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-200 px-5 py-3 font-bold text-slate-700">
                    Soal No. <?= $no++ ?>
                </div>
                <div class="p-5">
                    <div class="prose max-w-none text-sm text-slate-800 mb-4 p-4 bg-slate-50 rounded-lg border border-slate-100">
                        <?= $soal['pertanyaan'] ?>
                        <?php if ($soal['kunci_jawaban']): ?>
                            <div class="mt-3 pt-3 border-t border-slate-200 text-xs text-emerald-700 font-semibold">
                                <span class="bg-emerald-100 px-2 py-0.5 rounded">Referensi Guru:</span> <?= esc($soal['kunci_jawaban']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Jawaban Siswa:</label>
                    <div class="w-full p-4 bg-blue-50 border border-blue-100 rounded-lg text-slate-800 font-medium whitespace-pre-wrap leading-relaxed shadow-inner">
                        <?= $jawabanSiswa ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="fixed bottom-0 right-0 w-full lg:w-[calc(100%-16rem)] bg-white border-t border-slate-200 shadow-[0_-10px_20px_-5px_rgba(0,0,0,0.1)] p-4 z-50">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
            <p class="text-xs font-bold text-slate-500 uppercase">Nilai Pilihan Ganda Siswa Ini:</p>
            <p class="text-2xl font-black text-blue-600"><?= number_format($hasil['nilai_pg'] ?? 0, 1) ?></p>
        </div>

        <form action="/panel/penilaian/simpan-koreksi" method="POST" class="flex items-end gap-3 w-full sm:w-auto">
            <?= csrf_field() ?>
            <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
            <input type="hidden" name="siswa_id" value="<?= $siswa['id'] ?>">

            <div class="flex-1 sm:flex-none">
                <label class="text-[10px] font-bold text-indigo-600 uppercase block mb-1">Berikan Nilai Essai (0-100)</label>
                <input type="number" name="nilai_essai" value="<?= $hasil['nilai_essai'] ?? 0 ?>" min="0" max="100" step="0.1" required class="w-full sm:w-32 px-4 py-2 border-2 border-indigo-300 rounded-lg text-lg font-bold text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-center shadow-inner">
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold shadow-lg shadow-indigo-500/30 transition h-[46px]">
                💾 Simpan Nilai
            </button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>