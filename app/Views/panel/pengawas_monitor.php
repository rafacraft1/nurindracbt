<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <a href="/panel/ruang-pengawas" class="text-sm text-blue-600 hover:underline mb-1 inline-block">← Kembali ke Lobi</a>
        <h2 class="text-2xl font-bold text-slate-800 uppercase"><?= esc($jadwal['nama_mapel']) ?></h2>
        <p class="text-slate-500 font-semibold mt-1">🚪 <?= esc($jadwal['nama_ruangan']) ?> &nbsp;|&nbsp; ⏰ Durasi: <?= $jadwal['durasi'] ?> Menit</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="md:col-span-2 bg-slate-900 rounded-2xl shadow-lg p-6 flex flex-col items-center justify-center relative overflow-hidden border border-slate-700">
        <div class="absolute top-0 right-0 p-3 opacity-20 text-6xl">🔑</div>
        <p class="text-blue-400 text-sm font-bold uppercase tracking-wider mb-2 z-10">TOKEN UJIAN SAAT INI</p>
        <h1 id="displayTokenBesar" class="text-6xl md:text-7xl font-black text-white tracking-[0.2em] z-10 drop-shadow-[0_0_15px_rgba(59,130,246,0.5)]">
            <?= $token ?>
        </h1>
        <p class="text-slate-400 text-xs mt-3 z-10 text-center">Siswa wajib memasukkan token ini. Token otomatis berganti setiap 15 menit.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col justify-center">
        <h3 class="font-bold text-slate-700 mb-4 border-b pb-2">Aksi Token Pengawas</h3>

        <button type="button" onclick="konfirmasiTokenManual()" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 rounded-lg text-sm transition shadow-lg shadow-amber-500/30 mb-4 flex items-center justify-center">
            <span class="mr-2">🔄</span> Rilis Token Sekarang
        </button>

        <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl text-center shadow-inner">
            <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1">Auto-Generate Berikutnya</p>
            <div id="countdownToken" class="text-3xl font-mono font-black text-blue-700 tracking-widest">
                --:--
            </div>
            <p class="text-[9px] text-blue-500 mt-2 font-semibold">Tampilan akan me-refresh token secara otomatis.</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <h3 class="font-bold text-slate-700">Daftar Peserta di Ruangan (Total: <?= count($siswa) ?>)</h3>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">🔍</span>
                <input type="text" id="cariSiswa" placeholder="Cari Nama / NISN..." class="w-full pl-9 pr-3 py-1.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition">
            </div>
            <button onclick="window.location.reload()" class="text-xs bg-slate-200 hover:bg-slate-300 text-slate-700 px-3 py-1.5 rounded-lg font-bold transition whitespace-nowrap shrink-0">
                🔄 Refresh
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-100 text-slate-800 font-semibold uppercase text-[11px] tracking-wider">
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
                foreach ($siswa as $s): ?>
                    <tr class="hover:bg-slate-50 transition-colors row-siswa">
                        <td class="px-6 py-4 text-center font-medium"><?= $no++ ?></td>

                        <td class="px-6 py-4 nama-nisn-cell">
                            <div class="font-bold text-slate-800"><?= esc($s['nama_lengkap']) ?></div>
                            <div class="text-[11px] font-mono text-blue-600"><?= esc($s['nisn']) ?></div>
                        </td>

                        <td class="px-6 py-4 text-center border-x border-slate-100 bg-indigo-50/20">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" onchange="toggleHadir(<?= $jadwal['id'] ?>, <?= $s['id'] ?>, this)" class="sr-only peer" <?= (isset($s['is_hadir']) && $s['is_hadir'] == 1) ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                            <span class="block text-[10px] font-bold mt-1 label-hadir-<?= $s['id'] ?> <?= (isset($s['is_hadir']) && $s['is_hadir'] == 1) ? 'text-emerald-600' : 'text-slate-400' ?>">
                                <?= (isset($s['is_hadir']) && $s['is_hadir'] == 1) ? 'SUDAH DIABSEN' : 'BELUM HADIR' ?>
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <?php if ($s['is_login'] == 1): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 animate-pulse">
                                    🟢 ONLINE
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                    ⚪ OFFLINE
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <?php if ($s['status_ujian'] == 'completed'): ?>
                                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded border border-blue-200">✅ SELESAI</span>
                            <?php elseif ($s['status_ujian'] == 'progress'): ?>
                                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded border border-amber-200 animate-pulse">✍️ MENGERJAKAN</span>
                            <?php elseif ($s['status_ujian'] == 'pending'): ?>
                                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded border border-slate-200">⏳ SIAP UJIAN</span>
                            <?php else: ?>
                                <span class="text-xs font-medium text-slate-400">- Belum Mulai -</span>
                            <?php endif; ?>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form action="/panel/ruang-pengawas/reset-login/<?= $s['id'] ?>" method="POST" class="inline-block">
                                    <?= csrf_field() ?>
                                    <?php
                                    $btnResetClass = $s['is_login'] == 1
                                        ? 'bg-red-100 hover:bg-red-200 text-red-700 border border-red-200'
                                        : 'bg-slate-100 text-slate-300 cursor-not-allowed';
                                    ?>
                                    <button type="submit" <?= $s['is_login'] == 0 ? 'disabled' : '' ?> class="px-3 py-1 text-xs font-bold rounded <?= $btnResetClass ?> transition" title="Klik jika HP siswa mati/error agar bisa login lagi">
                                        🔓 Reset Login
                                    </button>
                                </form>

                                <?php if ($s['status_ujian'] == 'progress'): ?>
                                    <form action="/panel/ruang-pengawas/force-selesai/<?= $jadwal['id'] ?>/<?= $s['id'] ?>" method="POST" class="inline-block">
                                        <?= csrf_field() ?>
                                        <button type="submit" onclick="return confirm('Yakin ingin memaksa siswa ini selesai?')" class="px-3 py-1 text-xs font-bold rounded bg-slate-800 hover:bg-slate-900 text-white transition">
                                            ⏹️ Paksa
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($siswa)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500 border-dashed border-2 m-4">
                            Belum ada siswa yang di-plot ke ruangan ini.
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
    // ==========================================
    // ENGINE JAVASCRIPT GLOBAL
    // ==========================================
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // 1. ENGINE ABSENSI AJAX
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
                    csrfToken = data.csrfHash; // Sinkronisasi CSRF
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', csrfToken);

                    let label = document.querySelector('.label-hadir-' + siswaId);
                    label.innerText = data.is_hadir == 1 ? 'SUDAH DIABSEN' : 'BELUM HADIR';
                    label.className = data.is_hadir == 1 ? 'block text-[10px] font-bold mt-1 label-hadir-' + siswaId + ' text-emerald-600' : 'block text-[10px] font-bold mt-1 label-hadir-' + siswaId + ' text-slate-400';

                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: data.is_hadir == 1 ? 'success' : 'warning',
                        title: data.is_hadir == 1 ? 'Siswa ditandai Hadir!' : 'Absen dibatalkan.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    el.checked = !el.checked;
                }
            });
    }

    // 2. ENGINE AUTO-GENERATE TOKEN TUNGGAL (15 MENIT)
    let tokenCountdown = <?= $sisa_waktu ?? 900 ?>;
    let timerTokenInterval;

    function startTokenTimer() {
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
        let formData = new window.FormData();
        formData.append('<?= csrf_token() ?>', csrfToken);

        document.getElementById('displayTokenBesar').innerText = "⏳...";

        fetch(`/panel/ruang-pengawas/generate-token-ajax/<?= $jadwal['id'] ?>`, {
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

                    document.getElementById('displayTokenBesar').innerText = data.token;
                    tokenCountdown = 900;
                    startTokenTimer();

                    let msg = mode === 'auto' ? 'Token diperbarui otomatis!' : 'Token berhasil dirilis!';
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: msg,
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
    }

    function konfirmasiTokenManual() {
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

    // 3. ENGINE PENCARIAN SISWA (REAL-TIME CLIENT SIDE)
    document.getElementById('cariSiswa').addEventListener('input', function(e) {
        let keyword = e.target.value.toLowerCase();
        let rows = document.querySelectorAll('.row-siswa');

        rows.forEach(row => {
            let textData = row.querySelector('.nama-nisn-cell').textContent.toLowerCase();
            if (textData.includes(keyword)) {
                row.style.display = ''; // Munculkan baris
            } else {
                row.style.display = 'none'; // Sembunyikan baris
            }
        });
    });

    // Eksekusi Timer saat halaman pertama kali dimuat
    window.onload = () => {
        if ('<?= $token ?>' !== 'BELUM ADA') {
            if (tokenCountdown <= 0) requestNewToken('auto');
            else startTokenTimer();
        } else {
            document.getElementById('countdownToken').innerText = "STANDBY";
        }
    };
</script>
<?= $this->endSection() ?>