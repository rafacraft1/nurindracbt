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

<div class="mb-8 border-b border-slate-200 pb-5">
    <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Laporan & Penilaian</h2>
    <p class="text-slate-500 text-sm mt-1">Pilih kelas/jadwal untuk melihat rekapitulasi nilai dan mengoreksi soal essai secara manual.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    <?php foreach ($jadwal as $j): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:border-blue-300 hover:shadow-md transition-all flex flex-col justify-between group">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded-md uppercase tracking-wider border border-indigo-100 shadow-sm">
                        <?= $j['status'] === 'finished' ? 'Selesai' : 'Aktif' ?>
                    </span>
                    <span class="text-[11px] font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded border border-slate-100">
                        <?= date('d M Y', strtotime($j['waktu_mulai'])) ?>
                    </span>
                </div>

                <h3 class="text-lg font-black text-slate-800 uppercase leading-tight mb-2 group-hover:text-blue-600 transition-colors"><?= esc($j['nama_mapel']) ?></h3>

                <div class="flex items-center text-sm font-bold text-slate-600 mb-1">
                    <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    KLS: <?= esc($j['tingkat'] . ' ' . $j['jurusan']) ?>
                </div>
                <div class="flex items-center text-[11px] text-slate-500 mb-5 font-medium">
                    <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <?= esc($j['nama_ruangan']) ?>
                </div>
            </div>

            <div class="space-y-2 mt-auto">
                <a href="/panel/penilaian/detail/<?= $j['id'] ?>" class="flex items-center justify-center w-full bg-slate-800 hover:bg-slate-900 text-white py-2.5 rounded-xl text-sm font-bold shadow-sm transition transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Lihat Nilai & Koreksi
                </a>

                <?php if ($j['status'] === 'finished'): ?>
                    <button type="button" onclick="bukaModalSusulan(<?= $j['mapel_id'] ?>, '<?= $j['tingkat'] ?>', '<?= esc($j['nama_mapel']) ?>')" class="w-full bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 py-2 rounded-xl text-sm font-bold shadow-sm transition flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Susulan Gabungan
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($jadwal)): ?>
        <div class="col-span-full py-16 px-6 text-center bg-slate-50 rounded-2xl border-2 border-dashed border-slate-300">
            <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <p class="text-slate-600 font-bold text-lg">Belum ada jadwal ujian di kelas Anda.</p>
            <p class="text-sm text-slate-500 mt-1">Jadwal akan muncul di sini setelah Panitia membuatnya.</p>
        </div>
    <?php endif; ?>
</div>

<div id="modalSusulanGabungan" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0 py-6">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden transform scale-95 transition-transform flex flex-col max-h-full" id="modalSusulanContent">

        <div class="bg-purple-600 px-6 py-4 flex justify-between items-center shrink-0">
            <h3 class="font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Buat Susulan Gabungan Lintas Kelas
            </h3>
            <button type="button" onclick="tutupModalSusulan()" class="text-purple-200 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="/panel/penilaian/susulan-gabungan" method="POST" class="overflow-y-auto custom-scrollbar flex-1">
            <?= csrf_field() ?>
            <input type="hidden" name="mapel_id" id="susulan_mapel_id">
            <input type="hidden" name="tingkat" id="susulan_tingkat">

            <div class="p-6 space-y-6">
                <div class="bg-purple-50 border border-purple-200 p-4 rounded-xl text-sm text-purple-800 leading-relaxed shadow-sm">
                    <strong class="flex items-center mb-1">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Informasi Sistem:
                    </strong>
                    Sistem akan otomatis memindai dan mengumpulkan seluruh siswa yang <b>Belum Ujian</b> atau <b>Masih Mengerjakan</b> (karena kendala teknis) pada mata pelajaran <strong id="susulan_mapel_text" class="text-purple-900 uppercase underline decoration-2 underline-offset-2"></strong> tingkat <strong id="susulan_tingkat_text" class="text-purple-900 underline decoration-2 underline-offset-2"></strong> dari semua jurusan, lalu menggabungkannya ke dalam 1 ruangan pengawas baru.
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Jenis Ujian (Label)</label>
                        <select name="jenis_ujian_id" required class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 bg-white font-bold text-slate-800 shadow-sm transition">
                            <?php foreach ($jenis_ujian as $ju): ?>
                                <option value="<?= $ju['id'] ?>"><?= esc($ju['nama_ujian']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Ruangan Sentral</label>
                        <select name="ruangan_id" required class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 bg-white font-bold text-indigo-700 shadow-sm transition">
                            <?php foreach ($ruangan as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= esc($r['nama_ruangan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Guru Pengawas</label>
                        <select name="pengawas_id" required class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 bg-white font-bold text-slate-800 shadow-sm transition">
                            <option value="">-- Wajib Pilih --</option>
                            <?php foreach ($semua_guru as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= esc($g['nama_lengkap']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-5 rounded-xl bg-slate-50 border border-slate-200">
                    <div>
                        <label class="block text-xs font-bold text-purple-800 mb-1.5 uppercase tracking-wide">Waktu Mulai</label>
                        <input type="datetime-local" name="waktu_mulai" required class="w-full px-3 py-2 border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 font-mono text-[13px] bg-white shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-red-800 mb-1.5 uppercase tracking-wide">Waktu Selesai</label>
                        <input type="datetime-local" name="waktu_selesai" required class="w-full px-3 py-2 border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500 font-mono text-[13px] bg-white shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5 uppercase tracking-wide">Durasi Pengerjaan</label>
                        <div class="relative">
                            <input type="number" name="durasi" min="10" value="90" required class="w-full pl-3 pr-16 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 font-bold text-slate-800 bg-white shadow-sm text-center">
                            <span class="absolute right-3 top-2 text-xs font-bold text-slate-500">Menit</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 shrink-0">
                <button type="button" onclick="tutupModalSusulan()" class="px-5 py-2.5 border border-slate-300 bg-white rounded-xl text-slate-700 font-semibold hover:bg-slate-100 transition shadow-sm">Batalkan</button>
                <button type="submit" class="px-6 py-2.5 bg-purple-600 text-white rounded-xl hover:bg-purple-700 font-bold shadow-md shadow-purple-600/30 transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Eksekusi Susulan
                </button>
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