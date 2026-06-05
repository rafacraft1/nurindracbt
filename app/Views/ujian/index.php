<?php

/**
 * @var string $title
 * @var array $jadwalAktif
 * @var array $statusUjian
 * @var array $kehadiran
 */

$dbUjian = \Config\Database::connect();
$pengaturanUjian = $dbUjian->table('pengaturan')->where('id', 1)->get()->getRowArray();
$zonaWaktu = $pengaturanUjian['zona_waktu'] ?? 'Asia/Jakarta';
$logo = $pengaturanUjian['logo'] ?? null;

$labelZona = 'WIB';
if ($zonaWaktu === 'Asia/Makassar') $labelZona = 'WITA';
if ($zonaWaktu === 'Asia/Jayapura') $labelZona = 'WIT';

$urlLogo = $logo ? base_url('uploads/' . $logo) : base_url('assets/img/logo.png');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?></title>

    <link rel="icon" type="image/png" href="<?= $urlLogo ?>">

    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>

<body class="bg-slate-100 font-sans text-slate-800 min-h-screen flex flex-col">

    <header class="bg-blue-600 text-white shadow-md sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white rounded-md flex items-center justify-center p-0.5 shadow-sm shrink-0">
                    <img src="<?= $urlLogo ?>" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div class="hidden sm:block">
                    <h1 class="font-bold text-lg leading-tight tracking-wide">CBT PRO</h1>
                    <p class="text-[10px] text-blue-200">Portal Ujian Siswa</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right flex flex-col justify-center">
                    <span class="text-[9px] text-blue-200 font-bold tracking-wider uppercase">Waktu Server</span>
                    <span id="serverClock" class="font-mono text-sm sm:text-base font-bold bg-blue-800/50 px-2 py-0.5 rounded border border-blue-500/30">--:--:--</span>
                </div>
                <button onclick="confirmLogout()" class="bg-blue-700 hover:bg-red-500 text-xs font-bold px-3 py-2 sm:py-1.5 rounded-lg transition-colors border border-blue-500 shadow-sm flex items-center">
                    <svg class="w-4 h-4 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </div>
        </div>
    </header>

    <main class="flex-1 w-full max-w-4xl mx-auto p-4 md:p-6 pb-20">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 flex items-center gap-4 mb-6">
            <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl font-bold shrink-0 border-2 border-blue-200">
                <?= substr(session()->get('nama_lengkap'), 0, 1) ?>
            </div>
            <div class="flex-1 overflow-hidden">
                <h2 class="font-bold text-lg text-slate-800 truncate"><?= esc(session()->get('nama_lengkap')) ?></h2>
                <p class="text-xs font-mono text-slate-500 mt-0.5">NISN: <?= esc(session()->get('nisn')) ?></p>
                <div class="mt-2 flex gap-2 flex-wrap">
                    <span class="text-[10px] font-bold bg-slate-100 px-2 py-1 rounded text-slate-600 border border-slate-200">
                        KELAS: <?= esc(session()->get('tingkat') . ' ' . session()->get('jurusan') . ' ' . session()->get('rombel')) ?>
                    </span>
                </div>
            </div>
        </div>

        <h3 class="font-bold text-slate-700 mb-4 px-1 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Jadwal Ujian Tersedia
        </h3>

        <div class="space-y-4">
            <?php foreach ($jadwalAktif as $j):
                $statusPengerjaan = $statusUjian[$j['id']] ?? null;
                $isHadir = $kehadiran[$j['id']] ?? 0;
            ?>
                <div class="bg-white rounded-2xl shadow-sm border <?= $j['status'] == 'active' ? 'border-emerald-400 shadow-emerald-100' : 'border-slate-200' ?> overflow-hidden relative">

                    <?php if ($j['status'] == 'active'): ?>
                        <div class="absolute top-0 right-0 px-3 py-1 bg-emerald-500 text-white text-[10px] font-bold rounded-bl-lg animate-pulse">
                            TOKEN DIRILIS
                        </div>
                    <?php endif; ?>

                    <div class="p-5">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                            <?= esc($j['nama_ujian']) ?>
                        </div>
                        <h4 class="text-xl font-bold text-slate-800 uppercase mb-2"><?= esc($j['nama_mapel']) ?></h4>

                        <div class="flex items-center text-xs text-slate-600 gap-4 mb-4">
                            <span class="flex items-center"><svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg> <?= $j['durasi'] ?> Menit</span>
                            <span class="flex items-center"><svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg> <?= date('d M', strtotime((string)$j['waktu_mulai'])) ?></span>
                        </div>

                        <div class="border-t border-dashed border-slate-200 pt-4 mt-2">
                            <?php if ($statusPengerjaan === 'completed'): ?>
                                <button disabled class="w-full bg-slate-100 text-slate-400 font-bold py-3 rounded-xl border border-slate-200 flex justify-center items-center text-sm">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Ujian Selesai
                                </button>
                            <?php elseif ($statusPengerjaan === 'progress'): ?>
                                <a href="/ujian/kerjakan/<?= $j['id'] ?>" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 rounded-xl shadow-lg shadow-amber-500/30 transition flex justify-center items-center text-sm">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Lanjutkan Mengerjakan
                                </a>
                            <?php else: ?>
                                <?php if ($j['status'] === 'active'): ?>

                                    <?php if ($isHadir == 1): ?>
                                        <form action="/ujian/mulai" method="POST" class="flex flex-col sm:flex-row gap-2">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="jadwal_id" value="<?= $j['id'] ?>">
                                            <input type="text" name="token" placeholder="TOKEN (KOSONGKAN JIKA BEBAS)" autocomplete="off" class="flex-1 px-4 py-3 bg-slate-50 border-2 border-emerald-400 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono font-bold text-center tracking-widest uppercase text-emerald-800 placeholder:font-sans placeholder:tracking-normal placeholder:font-normal placeholder:text-sm transition">
                                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-emerald-500/30 transition text-sm whitespace-nowrap">
                                                MULAI
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="flex gap-2 opacity-80">
                                            <div class="flex-1 px-4 py-3 bg-slate-100 border border-slate-300 rounded-xl font-bold text-center text-slate-500 text-xs sm:text-sm flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                                MENUNGGU ABSEN PENGAWAS
                                            </div>
                                            <button type="button" onclick="window.location.reload()" class="bg-slate-700 hover:bg-slate-800 text-white font-bold px-4 py-3 rounded-xl shadow transition text-sm flex items-center justify-center">
                                                <svg class="w-5 h-5 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                <span class="hidden sm:inline">Cek</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <button disabled class="w-full bg-slate-100 text-slate-500 font-bold py-3 rounded-xl border border-slate-200 text-sm flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Menunggu Pengawas Merilis Token
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($jadwalAktif)): ?>
                <div class="bg-white p-10 rounded-2xl shadow-sm border border-slate-200 border-dashed text-center">
                    <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="font-bold text-slate-700">Tidak Ada Jadwal Tersedia</h3>
                    <p class="text-sm text-slate-500 mt-2">Belum ada ujian yang dijadwalkan untuk kelas/ruangan Anda saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        let labelZona = "<?= $labelZona ?>";
        let serverTime = new window.Date("<?= date('Y-m-d\TH:i:s') ?>");
        setInterval(() => {
            serverTime.setSeconds(serverTime.getSeconds() + 1);
            let h = String(serverTime.getHours()).padStart(2, '0');
            let m = String(serverTime.getMinutes()).padStart(2, '0');
            let s = String(serverTime.getSeconds()).padStart(2, '0');
            document.getElementById('serverClock').innerText = `${h}:${m}:${s} ${labelZona}`;
        }, 1000);

        function confirmLogout() {
            Swal.fire({
                title: 'Akhiri Sesi?',
                text: "Anda akan keluar dari aplikasi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Keluar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/logout';
                }
            })
        }

        function showToast(msg, type = 'success') {
            Toastify({
                text: msg,
                duration: 4000,
                gravity: "top",
                position: "center",
                style: {
                    background: type === 'success' ? "#10b981" : "#ef4444",
                    borderRadius: "10px",
                    marginTop: "10px"
                }
            }).showToast();
        }

        <?php if (session()->getFlashdata('error')) : ?>showToast("<?= esc(session()->getFlashdata('error'), 'js') ?>", 'error');
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')) : ?>showToast("<?= esc(session()->getFlashdata('success'), 'js') ?>", 'success');
        <?php endif; ?>
    </script>
</body>

</html>