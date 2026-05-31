<?php

/**
 * @var string $title
 * @var array $jadwal
 * @var array $siswa
 * @var string $token
 * @var int $sisa_waktu
 */

$waktuMulaiStr   = $jadwal['waktu_mulai'];
$waktuSelesaiStr = $jadwal['waktu_selesai'];
$waktuMulaiTs    = strtotime($waktuMulaiStr);
$waktuSelesaiTs  = strtotime($waktuSelesaiStr);
$sekarangTs      = time();

$isBelumMulai = $sekarangTs < $waktuMulaiTs;
$isSelesai    = $sekarangTs > $waktuSelesaiTs;

// FIX LINTER TAILWIND: Deklarasi kelas CSS secara dinamis di level PHP agar linter tidak mendeteksi konflik
$bgMonitorClass = $isBelumMulai ? 'bg-slate-100 border-slate-300' : ($isSelesai ? 'bg-red-900 border-red-700' : 'bg-slate-900 border-slate-700');
$btnRilisClass  = ($isBelumMulai || $isSelesai) ? 'bg-slate-200 text-slate-400 cursor-not-allowed border-slate-300' : 'bg-amber-500 hover:bg-amber-600 text-white shadow-lg shadow-amber-500/30';

?>
<?= $this->extend('layouts/panel') ?>
<?= $this->section('content') ?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <a href="/panel/ruang-pengawas" class="text-sm text-blue-600 hover:text-blue-800 font-bold mb-2 flex items-center group transition">
            <svg class="w-4 h-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Lobi Utama
        </a>
        <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tight"><?= esc($jadwal['nama_mapel']) ?> <?= strpos($jadwal['id_gabungan'], '-') !== false ? '<span class="bg-purple-100 text-purple-700 text-xs px-2.5 py-1 align-middle rounded-md ml-2 border border-purple-200">GABUNGAN KELAS</span>' : '' ?></h2>

        <div class="flex items-center gap-3 mt-2 flex-wrap">
            <span class="flex items-center text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <?= esc($jadwal['nama_ruangan']) ?>
            </span>
            <span class="flex items-center text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Jam Buka: <?= date('H:i', $waktuMulaiTs) ?> WIB
            </span>
            <span class="flex items-center text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                <svg class="w-4 h-4 mr-1.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Tutup: <?= date('H:i', $waktuSelesaiTs) ?> WIB
            </span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="md:col-span-2 rounded-2xl shadow-lg p-6 flex flex-col items-center justify-center relative overflow-hidden border <?= $bgMonitorClass ?>">

        <?php if ($isBelumMulai): ?>
            <svg class="absolute top-0 right-0 w-32 h-32 text-slate-300 opacity-50 -mt-6 -mr-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-2 z-10">Ujian Belum Dimulai</p>
            <h1 id="countdownMenujuMulai" class="text-5xl md:text-6xl font-black text-slate-700 tracking-widest z-10 mt-2">
                --:--:--
            </h1>
            <p class="text-slate-500 text-xs mt-4 z-10 text-center font-medium">Token akan otomatis tersedia ketika hitung mundur ini mencapai nol.</p>

        <?php elseif ($isSelesai): ?>
            <svg class="absolute top-0 right-0 w-32 h-32 text-white opacity-5 -mt-6 -mr-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-red-400 text-xs font-bold uppercase tracking-widest mb-2 z-10">Status Ruangan</p>
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-widest z-10 drop-shadow-[0_0_15px_rgba(239,68,68,0.8)] mt-2">
                UJIAN DITUTUP
            </h1>
            <p class="text-red-200 text-xs mt-4 z-10 text-center font-medium">Sistem telah menghentikan akses ujian. Siswa tidak dapat login lagi.</p>

        <?php else: ?>
            <svg class="absolute top-0 right-0 w-32 h-32 text-white opacity-5 -mt-6 -mr-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
            <p class="text-blue-400 text-xs font-bold uppercase tracking-widest mb-2 z-10 flex items-center">
                <span class="w-2 h-2 rounded-full bg-blue-500 mr-2 animate-pulse"></span> Token Ujian Aktif
            </p>
            <h1 id="displayTokenBesar" class="text-6xl md:text-7xl font-black text-white tracking-[0.25em] z-10 drop-shadow-[0_0_20px_rgba(59,130,246,0.6)] mt-2">
                <?= $token ?>
            </h1>
            <p class="text-slate-400 text-xs mt-4 z-10 text-center font-medium">Siswa wajib memasukkan token ini saat masuk. Token berganti otomatis.</p>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col justify-center">
        <h3 class="font-bold text-slate-800 mb-4 border-b border-slate-200 pb-2">Kontrol Token</h3>

        <button type="button" id="btnRilisManual" onclick="konfirmasiTokenManual()" <?= ($isBelumMulai || $isSelesai) ? 'disabled' : '' ?> class="w-full <?= $btnRilisClass ?> font-bold py-3.5 rounded-xl text-sm transition mb-5 flex items-center justify-center transform hover:-translate-y-0.5 border">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <?= $isBelumMulai ? 'Belum Waktunya' : ($isSelesai ? 'Jadwal Ditutup' : 'Rilis Token Sekarang') ?>
        </button>

        <div class="bg-blue-50 border border-blue-100 p-5 rounded-xl text-center shadow-inner relative overflow-hidden">
            <div class="absolute inset-0 bg-blue-600/5 -skew-y-12 transform origin-top-left"></div>
            <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-2 relative z-10">Auto-Generate Berikutnya</p>
            <div id="countdownToken" class="text-4xl font-mono font-black text-blue-700 tracking-widest relative z-10">
                --:--
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h3 class="font-bold text-slate-800 text-base">Peserta di Ruangan <span class="bg-slate-800 text-white px-2 py-0.5 rounded-md text-xs ml-2"><?= count($siswa) ?></span></h3>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="cariSiswa" placeholder="Cari Nama / NISN..." class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition shadow-sm text-slate-700">
            </div>
            <button onclick="window.location.reload()" class="flex items-center text-sm bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 px-4 py-2 rounded-xl font-bold transition whitespace-nowrap shrink-0 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh Data
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600 min-w-[900px]">
            <thead class="bg-slate-100 text-slate-800 font-bold uppercase text-[10px] tracking-wider">
                <tr>
                    <th class="px-6 py-4 w-16 text-center">No</th>
                    <th class="px-6 py-4">Siswa</th>
                    <th class="px-6 py-4 text-center border-x border-slate-200 bg-indigo-50/50">Kehadiran Fisik</th>
                    <th class="px-6 py-4 text-center">Status Sesi</th>
                    <th class="px-6 py-4 text-center">Progres Ujian</th>
                    <th class="px-6 py-4 text-center">Aksi / Bantuan</th>
                </tr>
            </thead>
            <tbody id="tabelSiswa" class="divide-y divide-slate-100">
                <?php $no = 1;
                foreach ($siswa as $s):
                    $idJadwalKirim = !empty($s['actual_jadwal_id']) ? $s['actual_jadwal_id'] : $jadwal['id'];
                ?>
                    <tr class="hover:bg-blue-50/30 transition-colors h-16 row-siswa">
                        <td class="px-6 py-4 text-center font-medium"><?= $no++ ?></td>

                        <td class="px-6 py-4 nama-nisn-cell">
                            <div class="font-black text-slate-800 uppercase tracking-wide"><?= esc($s['nama_lengkap']) ?></div>
                            <div class="text-[11px] font-mono text-slate-500 mt-0.5">
                                NISN: <span class="text-blue-600 font-bold"><?= esc($s['nisn']) ?></span> <span class="text-slate-300 mx-1">&bull;</span> <strong class="text-purple-700 bg-purple-50 px-1.5 py-0.5 border border-purple-100 rounded"><?= esc($s['tingkat'] . ' ' . $s['jurusan']) ?></strong>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-center border-x border-slate-100 bg-indigo-50/20">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" onchange="toggleHadir(<?= $idJadwalKirim ?>, <?= $s['id'] ?>, this)" class="sr-only peer" <?= (isset($s['is_hadir']) && $s['is_hadir'] == 1) ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                            </label>
                            <span class="block text-[10px] font-bold mt-1.5 tracking-wider label-hadir-<?= $s['id'] ?> <?= (isset($s['is_hadir']) && $s['is_hadir'] == 1) ? 'text-emerald-600' : 'text-slate-400' ?>">
                                <?= (isset($s['is_hadir']) && $s['is_hadir'] == 1) ? 'SUDAH HADIR' : 'BELUM HADIR' ?>
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <?php if ($s['is_login'] == 1): ?>
                                <span class="inline-flex items-center px-2.5 py-1.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span> ONLINE
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                    <span class="w-2 h-2 rounded-full bg-slate-400 mr-1.5"></span> OFFLINE
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <?php if ($s['status_ujian'] == 'completed'): ?>
                                <span class="inline-flex items-center text-[10px] font-bold text-blue-700 bg-blue-50 px-2.5 py-1.5 rounded-md border border-blue-200">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    SELESAI
                                </span>
                            <?php elseif ($s['status_ujian'] == 'progress'): ?>
                                <span class="inline-flex items-center text-[10px] font-bold text-amber-700 bg-amber-50 px-2.5 py-1.5 rounded-md border border-amber-200">
                                    <svg class="w-3.5 h-3.5 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    MENGERJAKAN
                                </span>
                            <?php elseif ($s['status_ujian'] == 'pending'): ?>
                                <span class="inline-flex items-center text-[10px] font-bold text-slate-600 bg-slate-100 px-2.5 py-1.5 rounded-md border border-slate-200">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    SIAP UJIAN
                                </span>
                            <?php else: ?>
                                <span class="text-xs font-medium text-slate-400">- Belum Mulai -</span>
                            <?php endif; ?>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form action="/panel/ruang-pengawas/reset-login/<?= $s['id'] ?>" method="POST" id="formReset<?= $s['id'] ?>" class="inline-block">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="jadwal_id" value="<?= $idJadwalKirim ?>">
                                    <?php
                                    $btnResetClass = $s['is_login'] == 1
                                        ? 'bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-200 cursor-pointer shadow-sm'
                                        : 'bg-slate-50 text-slate-300 border border-slate-200 cursor-not-allowed';
                                    ?>
                                    <button type="button" <?= $s['is_login'] == 0 ? 'disabled' : "onclick=\"konfirmasiReset({$s['id']}, '" . htmlspecialchars($s['nama_lengkap'], ENT_QUOTES, 'UTF-8') . "')\"" ?> class="px-3 py-1.5 text-xs font-bold rounded-lg <?= $btnResetClass ?> transition flex items-center" title="Klik jika perangkat siswa terputus dari jaringan agar bisa login kembali">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                        </svg>
                                        Reset Login
                                    </button>
                                </form>

                                <?php if ($s['status_ujian'] == 'progress'): ?>
                                    <form action="/panel/ruang-pengawas/force-selesai/<?= $idJadwalKirim ?>/<?= $s['id'] ?>" method="POST" id="formPaksa<?= $s['id'] ?>" class="inline-block">
                                        <?= csrf_field() ?>
                                        <button type="button" onclick="konfirmasiPaksa(<?= $s['id'] ?>, '<?= htmlspecialchars($s['nama_lengkap'], ENT_QUOTES, 'UTF-8') ?>')" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 shadow-sm transition flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                                            </svg>
                                            Paksa Selesai
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($siswa)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 bg-slate-50">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span class="font-bold text-slate-600 block">Belum ada siswa yang di-plot ke ruangan ini.</span>
                            <span class="text-xs text-slate-400">Hubungi panitia jika ini merupakan kesalahan sistem.</span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let isBelumMulai = <?= $isBelumMulai ? 'true' : 'false' ?>;
    let isSelesai = <?= $isSelesai ? 'true' : 'false' ?>;
    let serverTime = <?= $sekarangTs * 1000 ?>;
    let startTime = <?= $waktuMulaiTs * 1000 ?>;

    if (isBelumMulai) {
        let timerMulai = setInterval(() => {
            serverTime += 1000;
            let diff = Math.max(0, startTime - serverTime);

            if (diff <= 0) {
                clearInterval(timerMulai);
                window.location.reload();
            } else {
                let h = String(Math.floor((diff / (1000 * 60 * 60)) % 24)).padStart(2, '0');
                let m = String(Math.floor((diff / 1000 / 60) % 60)).padStart(2, '0');
                let s = String(Math.floor((diff / 1000) % 60)).padStart(2, '0');
                let targetDOM = document.getElementById('countdownMenujuMulai');
                if (targetDOM) targetDOM.innerText = `${h}:${m}:${s}`;
            }
        }, 1000);
    }

    function konfirmasiPaksa(idSiswa, namaSiswa) {
        Swal.fire({
            title: 'Hentikan Ujian?',
            html: `Anda akan memaksa sistem untuk mengirimkan ujian milik <strong class="text-red-600">${namaSiswa}</strong>.<br><br>Aksi ini tidak dapat dibatalkan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Paksa Selesai!'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('formPaksa' + idSiswa).submit();
        });
    }

    function konfirmasiReset(idSiswa, namaSiswa) {
        Swal.fire({
            title: 'Reset Sesi Login?',
            html: `Sesi login milik <strong class="text-amber-600">${namaSiswa}</strong> akan diakhiri paksa dari server agar ia bisa login kembali di perangkatnya.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Reset Sesi!'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('formReset' + idSiswa).submit();
        });
    }

    function toggleHadir(jadwalId, siswaId, el) {
        let formData = new window.FormData();
        formData.append('<?= csrf_token() ?>', csrfToken);

        fetch(`/panel/ruang-pengawas/tandai-hadir/${jadwalId}/${siswaId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    csrfToken = data.csrfHash;
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', csrfToken);

                    let label = document.querySelector('.label-hadir-' + siswaId);
                    label.innerText = data.is_hadir == 1 ? 'SUDAH HADIR' : 'BELUM HADIR';
                    label.className = data.is_hadir == 1 ? 'block text-[10px] font-bold mt-1.5 tracking-wider label-hadir-' + siswaId + ' text-emerald-600' : 'block text-[10px] font-bold mt-1.5 tracking-wider label-hadir-' + siswaId + ' text-slate-400';

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: data.is_hadir == 1 ? 'success' : 'warning',
                        title: data.is_hadir == 1 ? 'Siswa ditandai Hadir!' : 'Absen dibatalkan.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    el.checked = !el.checked;
                    Swal.fire('Gagal!', data.message || 'Terjadi kesalahan sistem.', 'error');
                }
            })
            .catch(() => {
                el.checked = !el.checked;
                Swal.fire('Error!', 'Jaringan terputus.', 'error');
            });
    }

    let tokenCountdown = <?= $sisa_waktu ?? 900 ?>;
    let timerTokenInterval;

    function startTokenTimer() {
        if (isBelumMulai || isSelesai) return;

        clearInterval(timerTokenInterval);
        timerTokenInterval = setInterval(() => {
            if (tokenCountdown <= 0) {
                clearInterval(timerTokenInterval);
                document.getElementById('countdownToken').innerText = "00:00";
                requestNewToken('auto');
            } else {
                tokenCountdown--;
                let m = String(Math.floor(tokenCountdown / 60)).padStart(2, '0');
                let s = String(tokenCountdown % 60).padStart(2, '0');
                document.getElementById('countdownToken').innerText = `${m}:${s}`;
            }
        }, 1000);
    }

    function requestNewToken(mode = 'manual') {
        if (isBelumMulai || isSelesai) {
            Swal.fire('Akses Ditolak!', 'Waktu ujian tidak mengizinkan rilis token saat ini.', 'error');
            return;
        }

        let formData = new window.FormData();
        formData.append('<?= csrf_token() ?>', csrfToken);

        document.getElementById('displayTokenBesar').innerText = "⏳...";

        fetch(`/panel/ruang-pengawas/generate-token-ajax/<?= $jadwal['id_gabungan'] ?>`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                csrfToken = data.csrfHash;
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', csrfToken);

                if (data.success) {
                    document.getElementById('displayTokenBesar').innerText = data.token;
                    tokenCountdown = 900;
                    startTokenTimer();

                    let msg = mode === 'auto' ? 'Token otomatis diperbarui!' : 'Token berhasil dirilis serentak!';
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: msg,
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    document.getElementById('displayTokenBesar').innerText = "ERROR";
                    Swal.fire('Gagal Merilis Token', data.message, 'error');
                }
            })
            .catch(() => {
                document.getElementById('displayTokenBesar').innerText = "TIMEOUT";
                Swal.fire('Koneksi Terputus', 'Gagal menyambung ke server.', 'error');
            });
    }

    function konfirmasiTokenManual() {
        if (isBelumMulai) {
            Swal.fire('Belum Waktunya!', 'Tunggu hingga jam ujian dimulai untuk merilis token.', 'info');
            return;
        }

        Swal.fire({
            title: 'Rilis Token Baru?',
            text: "Timer 15 menit akan di-reset dari awal. Siswa yang sudah masuk soal tidak akan terganggu sama sekali.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Rilis Token!'
        }).then((result) => {
            if (result.isConfirmed) {
                requestNewToken('manual');
            }
        });
    }

    document.getElementById('cariSiswa').addEventListener('input', function(e) {
        let keyword = e.target.value.toLowerCase();
        let rows = document.querySelectorAll('.row-siswa');

        rows.forEach(row => {
            let textData = row.querySelector('.nama-nisn-cell').textContent.toLowerCase();
            if (textData.includes(keyword)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    window.onload = () => {
        if (!isBelumMulai && !isSelesai) {
            if ('<?= $token ?>' !== 'BELUM ADA') {
                if (tokenCountdown <= 0) requestNewToken('auto');
                else startTokenTimer();
            } else {
                document.getElementById('countdownToken').innerText = "STANDBY";
            }
        } else {
            document.getElementById('countdownToken').innerText = "--:--";
        }
    };
</script>
<?= $this->endSection() ?>