<?php

/**
 * @var array $jadwal
 * @var array $jenis_ujian
 * @var array $ruangan
 * @var array $semua_guru
 */
?>
<?= $this->extend('layouts/panel') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800">Laporan & Penilaian</h2>
    <p class="text-slate-500 text-sm mt-1">Pilih kelas/jadwal untuk melihat nilai dan mengoreksi essai.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($jadwal as $j): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-bold rounded uppercase tracking-wider border border-indigo-100">
                        <?= $j['status'] === 'finished' ? 'Selesai' : 'Aktif' ?>
                    </span>
                    <span class="text-xs font-bold text-slate-400"><?= date('d M Y', strtotime($j['waktu_mulai'])) ?></span>
                </div>
                <h3 class="text-lg font-bold text-slate-800 uppercase leading-tight mb-1"><?= esc($j['nama_mapel']) ?></h3>
                <p class="text-sm font-semibold text-slate-600 mb-4">Kelas: <?= esc($j['tingkat'] . ' ' . $j['jurusan']) ?> <span class="text-xs text-slate-400 font-normal">(&#128205; <?= esc($j['nama_ruangan']) ?>)</span></p>
            </div>

            <div>
                <a href="/panel/penilaian/detail/<?= $j['id'] ?>" class="block text-center w-full bg-slate-800 hover:bg-slate-900 text-white py-2.5 rounded-lg text-sm font-bold transition shadow-sm mb-2">
                    Lihat Nilai & Koreksi
                </a>

                <?php if ($j['status'] === 'finished'): ?>
                    <button type="button" onclick="bukaModalSusulan(<?= $j['mapel_id'] ?>, '<?= $j['tingkat'] ?>', '<?= esc($j['nama_mapel']) ?>')" class="w-full text-center bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 py-2 rounded-lg text-sm font-bold transition shadow-sm flex items-center justify-center gap-2">
                        <span>🔄</span> Susulan Gabungan
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($jadwal)): ?>
        <div class="col-span-full p-12 text-center bg-slate-50 rounded-xl border border-dashed border-slate-300">
            <span class="text-4xl mb-3 block">📭</span>
            <p class="text-slate-500 font-bold">Belum ada jadwal ujian di kelas Anda.</p>
        </div>
    <?php endif; ?>
</div>

<div id="modalSusulanGabungan" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden transform scale-95 transition-transform" id="modalSusulanContent">
        <div class="bg-purple-600 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white flex items-center gap-2"><span>🔄</span> Buat Susulan Gabungan</h3>
            <button type="button" onclick="tutupModalSusulan()" class="text-purple-200 hover:text-white">✖</button>
        </div>

        <form action="/panel/penilaian/susulan-gabungan" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="mapel_id" id="susulan_mapel_id">
            <input type="hidden" name="tingkat" id="susulan_tingkat">

            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div class="bg-purple-50 border border-purple-200 p-3 rounded-lg text-sm text-purple-800 font-medium">
                    Sistem otomatis mengumpulkan siswa yang belum tuntas di mapel <strong id="susulan_mapel_text" class="text-purple-900 uppercase"></strong> tingkat <strong id="susulan_tingkat_text" class="text-purple-900"></strong> lintas jurusan menjadi 1 layar Pengawas.
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase">Jenis Ujian (Label)</label>
                        <select name="jenis_ujian_id" required class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-purple-500 bg-white font-semibold">
                            <?php foreach ($jenis_ujian as $ju): ?>
                                <option value="<?= $ju['id'] ?>"><?= esc($ju['nama_ujian']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase">Ruangan Sentral</label>
                        <select name="ruangan_id" required class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-purple-500 bg-white font-bold text-indigo-700">
                            <?php foreach ($ruangan as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= esc($r['nama_ruangan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase">Guru Pengawas</label>
                        <select name="pengawas_id" required class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-purple-500 bg-white font-semibold">
                            <option value="">-- Wajib Pilih --</option>
                            <?php foreach ($semua_guru as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= esc($g['nama_lengkap']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-lg bg-slate-50 border border-slate-200 mt-2">
                    <div>
                        <label class="block text-xs font-bold text-purple-800 mb-1 uppercase">Waktu Mulai</label>
                        <input type="datetime-local" name="waktu_mulai" required class="w-full px-3 py-2 border border-purple-200 rounded focus:ring-2 focus:ring-purple-500 font-mono text-[13px]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-red-800 mb-1 uppercase">Waktu Selesai</label>
                        <input type="datetime-local" name="waktu_selesai" required class="w-full px-3 py-2 border border-red-200 rounded focus:ring-2 focus:ring-red-500 font-mono text-[13px]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1 uppercase">Durasi Pengerjaan</label>
                        <div class="relative">
                            <input type="number" name="durasi" min="10" value="90" required class="w-full pl-3 pr-12 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-purple-500 font-bold text-slate-700">
                            <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-500">Menit</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t flex justify-end gap-3">
                <button type="button" onclick="tutupModalSusulan()" class="px-4 py-2 border rounded-lg text-slate-700 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-bold shadow-md transition">Eksekusi Susulan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const mSusulan = document.getElementById('modalSusulanGabungan');
    const cSusulan = document.getElementById('modalSusulanContent');

    function bukaModalSusulan(mapelId, tingkat, mapelNama) {
        document.getElementById('susulan_mapel_id').value = mapelId;
        document.getElementById('susulan_tingkat').value = tingkat;
        document.getElementById('susulan_mapel_text').innerText = mapelNama;
        document.getElementById('susulan_tingkat_text').innerText = tingkat;

        mSusulan.classList.remove('hidden');
        mSusulan.classList.add('flex');
        setTimeout(() => {
            mSusulan.classList.remove('opacity-0');
            cSusulan.classList.remove('scale-95');
        }, 10);
    }

    function tutupModalSusulan() {
        mSusulan.classList.add('opacity-0');
        cSusulan.classList.add('scale-95');
        setTimeout(() => {
            mSusulan.classList.add('hidden');
            mSusulan.classList.remove('flex');
        }, 300);
    }
</script>
<?= $this->endSection() ?>