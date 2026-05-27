<?php

/**
 * @var array $siswa
 * @var array $staff
 * @var array|null $pengaturan
 * @var array $listTingkat
 * @var array $listJurusan
 * @var array $listRombel
 * @var string $filterTingkat
 * @var string $filterJurusan
 * @var string $filterRombel
 */
?>
<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<style>
    /* ===================================================
       STYLE UMUM (Layar & Printer)
       =================================================== */
    .grid-siswa {
        display: grid;
        grid-template-columns: repeat(2, 85.6mm);
        grid-auto-rows: 43.98mm;
        gap: 5mm;
        justify-content: center;
        margin: 0 auto;
    }

    @media (max-width: 740px) {
        .grid-siswa {
            grid-template-columns: repeat(auto-fit, 85.6mm);
        }
    }

    .kartu-siswa {
        width: 85.6mm;
        height: 43.98mm;
        box-sizing: border-box;
        padding: 10px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background-color: white;
        border: 2px solid #1e293b;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        transition: all 0.2s ease-in-out;
    }

    /* Style Mode Pilih (Hanya di layar browser) */
    .kartu-siswa.mode-pilih {
        cursor: pointer;
        position: relative;
    }

    .kartu-siswa.mode-pilih:hover {
        border-color: #3b82f6;
    }

    .kartu-siswa.mode-pilih.dipilih {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
        background-color: #f8fafc;
    }

    /* Optimasi Render / Lazy Load DOM */
    @media screen {

        .page-container,
        .kartu-item {
            content-visibility: auto;
            contain-intrinsic-size: auto 1000px;
        }
    }

    /* ===================================================
       CSS KHUSUS PRINTER
       =================================================== */
    @media print {
        @page {
            size: A4 portrait;
            margin-top: 30mm;
            /* Jarak 3 cm dari ujung kertas atas */
            margin-bottom: 5mm;
            margin-left: 5mm;
            margin-right: 5mm;
        }

        html,
        body {
            height: auto !important;
            overflow: visible !important;
            background-color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Sembunyikan elemen yang tidak perlu dicetak */
        aside,
        header,
        .no-print,
        .kartu-checkbox {
            display: none !important;
        }

        main {
            overflow: visible !important;
            height: auto !important;
            background: white !important;
            padding: 0 !important;
        }

        .tab-content:not(.active-print) {
            display: none !important;
        }

        /* Jika mode cetak spesifik, sembunyikan kartu yang tidak dicentang */
        body.cetak-spesifik .kartu-siswa:not(.dipilih) {
            display: none !important;
        }

        /* Pembagi halaman maksimal 10 kartu (5 baris x 2 kolom) */
        .page-container {
            page-break-after: always !important;
            break-after: page !important;
        }

        .page-container:last-child {
            page-break-after: avoid !important;
            break-after: avoid !important;
        }

        /* Pengamanan ukuran grid printer */
        .grid-siswa {
            display: grid !important;
            grid-template-columns: repeat(2, 85.6mm) !important;
            grid-auto-rows: 43.98mm !important;
            gap: 4mm !important;
            justify-content: center !important;
        }

        .kartu-siswa {
            box-shadow: none !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            margin: 0 !important;
            background-color: white !important;
            /* Paksa putih saat cetak spesifik */
        }

        /* Grid Staff */
        #tab-staff .print-grid {
            display: block !important;
        }

        #tab-staff .print-grid::after {
            content: "";
            display: table;
            clear: both;
        }

        #tab-staff .kartu-item {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            float: left !important;
            width: calc(50% - 0.5rem) !important;
            margin-bottom: 1rem !important;
        }

        #tab-staff .kartu-item:nth-child(odd) {
            margin-right: 1rem !important;
        }

        #tab-staff .kartu-item:nth-child(even) {
            margin-right: 0 !important;
        }
    }
</style>

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Cetak Kartu Login</h2>
        <p class="text-slate-500 text-sm mt-1">Cetak kredensial login dan pilih filter sesuai kebutuhan.</p>
    </div>

    <div class="flex gap-2 w-full md:w-auto">
        <button onclick="togglePilihSpesifik()" id="btnModePilih" class="flex-1 md:flex-none bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 px-4 py-2.5 rounded-lg font-bold text-sm transition shadow-sm flex items-center justify-center">
            <span class="mr-2">☑️</span> Pilih Kartu Spesifik
        </button>
        <button onclick="prosesCetak()" id="btnCetak" class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow-lg shadow-blue-500/30 flex items-center justify-center">
            <span class="mr-2 text-lg">🖨️</span> Cetak Semua
        </button>
    </div>
</div>

<div class="bg-white p-4 rounded-xl border border-slate-200 mb-6 no-print shadow-sm" id="filterArea">
    <form action="" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[150px]">
            <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Tingkat</label>
            <select name="tingkat" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">Semua Tingkat</option>
                <?php foreach ($listTingkat as $t): if (empty($t['tingkat'])) continue; ?>
                    <option value="<?= $t['tingkat'] ?>" <?= ($filterTingkat == $t['tingkat']) ? 'selected' : '' ?>><?= $t['tingkat'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex-1 min-w-[150px]">
            <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Jurusan</label>
            <select name="jurusan" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">Semua Jurusan</option>
                <?php foreach ($listJurusan as $j): if (empty($j['jurusan'])) continue; ?>
                    <option value="<?= $j['jurusan'] ?>" <?= ($filterJurusan == $j['jurusan']) ? 'selected' : '' ?>><?= $j['jurusan'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex-1 min-w-[150px]">
            <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Rombel</label>
            <select name="rombel" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">Semua Rombel</option>
                <?php foreach ($listRombel as $r): if (empty($r['rombel'])) continue; ?>
                    <option value="<?= $r['rombel'] ?>" <?= ($filterRombel == $r['rombel']) ? 'selected' : '' ?>><?= $r['rombel'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2 rounded-lg text-sm font-semibold transition shadow-sm h-[38px] flex items-center">
                Terapkan Filter
            </button>
        </div>

        <?php if (!empty($filterTingkat) || !empty($filterJurusan) || !empty($filterRombel)): ?>
            <div>
                <a href="<?= base_url('panel/cetak-kartu') ?>" class="bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 px-4 py-2 rounded-lg text-sm font-semibold transition h-[38px] flex items-center">
                    Reset
                </a>
            </div>
        <?php endif; ?>
    </form>
</div>

<div class="flex space-x-2 border-b border-slate-300 mb-6 no-print">
    <button onclick="switchTab('siswa')" id="btn-tab-siswa" class="px-6 py-3 font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors flex items-center">
        👨‍🎓 Kartu Siswa
        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full ml-2"><?= count($siswa) ?></span>
    </button>
    <button onclick="switchTab('staff')" id="btn-tab-staff" class="px-6 py-3 font-semibold text-slate-500 hover:text-blue-600 border-b-2 border-transparent transition-colors flex items-center">
        👨‍🏫 Kartu Staff / Pengawas
        <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-full ml-2"><?= count($staff) ?></span>
    </button>
</div>

<div id="tab-siswa" class="tab-content active-print block">
    <?php if (!empty($siswa)): ?>
        <?php foreach (array_chunk($siswa, 10) as $index => $chunk): ?>
            <div class="page-container <?= $index > 0 ? 'mt-10 print:mt-0' : '' ?>">
                <div class="grid-siswa">
                    <?php foreach ($chunk as $s): ?>
                        <div class="kartu-siswa relative" data-id="<?= $s['id'] ?>" onclick="togglePilihKartu(this)">

                            <div class="kartu-checkbox absolute -top-2 -right-2 hidden z-10 bg-white rounded-full">
                                <input type="checkbox" class="w-6 h-6 rounded-full border-2 border-slate-300 text-blue-600 focus:ring-blue-500 pointer-events-none cursor-pointer">
                            </div>

                            <div class="flex items-center border-b-2 border-slate-800 pb-1 mb-[3mm]">
                                <div class="w-8 h-8 border border-slate-400 flex items-center justify-center mr-2 rounded-full shrink-0 overflow-hidden bg-white">
                                    <?php if (!empty($pengaturan['logo'])): ?>
                                        <img src="<?= base_url('uploads/' . $pengaturan['logo']) ?>" loading="lazy" alt="Logo" class="w-full h-full object-contain p-0.5">
                                    <?php else: ?>
                                        <span class="text-[7px] text-slate-500 font-bold">LOGO</span>
                                    <?php endif; ?>
                                </div>
                                <div class="leading-tight">
                                    <h2 class="font-bold text-xs uppercase text-slate-800">Kartu Login CBT</h2>
                                    <p class="text-[9px] text-slate-600">
                                        <?= esc($pengaturan['nama_sekolah'] ?? 'Ujian Berbasis Komputer & Smartphone') ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-2 h-full items-start">
                                <div class="w-14 h-16 border border-slate-400 bg-slate-100 flex flex-col items-center justify-center shrink-0">
                                    <span class="text-[8px] text-slate-400 font-semibold">3 x 4</span>
                                </div>

                                <div class="flex-1 text-[10px] text-slate-800">
                                    <table class="w-full table-fixed">
                                        <tr>
                                            <td class="font-bold align-top w-12 leading-tight">Nama</td>
                                            <td class="w-2 align-top leading-tight">:</td>
                                            <td class="font-bold uppercase whitespace-normal break-words leading-tight align-top pb-0.5"><?= esc($s['nama_lengkap']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold align-top leading-tight">Kelas</td>
                                            <td class="align-top leading-tight">:</td>
                                            <td class="align-top leading-tight pb-0.5"><?= esc($s['tingkat'] . ' ' . $s['jurusan'] . ' ' . $s['rombel']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold align-top leading-tight">Ruangan</td>
                                            <td class="align-top leading-tight">:</td>
                                            <td class="align-top leading-tight pb-0.5">
                                                <span class="font-bold text-blue-700 bg-blue-50 px-1 rounded inline-block">
                                                    <?= esc($s['nama_ruangan'] ?? 'Belum Diplot') ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="py-0.5">
                                                <div class="border-t border-dashed border-slate-400 my-0.5"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold leading-tight text-blue-700">NISN</td>
                                            <td class="font-bold leading-tight text-blue-700">:</td>
                                            <td class="font-mono font-bold leading-tight text-blue-700 text-[10px]"><?= esc($s['nisn']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold leading-tight text-red-600">Password</td>
                                            <td class="font-bold leading-tight text-red-600">:</td>
                                            <td class="font-mono font-bold leading-tight text-red-600 text-[10px]"><?= esc($s['password_plain'] ?? 'siswa123') ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-span-full p-12 text-center text-slate-500 no-print border-2 border-dashed border-slate-300 rounded-xl bg-slate-50">
            <span class="text-4xl block mb-3">🔍</span>
            <p class="font-bold text-slate-600">Data Tidak Ditemukan</p>
            <p class="text-xs text-slate-400 mt-1">Belum ada data siswa atau tidak cocok dengan filter pencarian.</p>
        </div>
    <?php endif; ?>
</div>

<div id="tab-staff" class="tab-content hidden">
    <div class="print-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <?php foreach ($staff as $st): ?>
            <div class="kartu-item border-2 border-slate-800 rounded-xl overflow-hidden bg-white relative text-center shadow-sm">
                <?php
                $bgClass = 'bg-blue-600';
                $roleText = 'Guru / Pengawas';
                if ($st['role'] == 'admin') {
                    $bgClass = 'bg-slate-800';
                    $roleText = 'Super Administrator';
                } else if ($st['is_panitia'] == 1) {
                    $bgClass = 'bg-emerald-600';
                    $roleText = 'Panitia Ujian';
                }
                ?>
                <div class="<?= $bgClass ?> text-white p-3 border-b-2 border-slate-800 flex justify-center items-center gap-2">
                    <?php if (!empty($pengaturan['logo'])): ?>
                        <div class="w-5 h-5 bg-white rounded-full overflow-hidden flex items-center justify-center shrink-0">
                            <img src="<?= base_url('uploads/' . $pengaturan['logo']) ?>" loading="lazy" alt="Logo" class="w-full h-full object-contain p-[2px]">
                        </div>
                    <?php endif; ?>
                    <h2 class="font-bold text-xs uppercase tracking-wider">ID CARD STAFF</h2>
                </div>

                <div class="p-4 flex flex-col items-center">
                    <div class="w-20 h-24 border-2 border-slate-400 bg-slate-100 flex items-center justify-center mb-3">
                        <span class="text-[10px] text-slate-400 font-semibold">FOTO</span>
                    </div>
                    <h3 class="font-bold text-sm text-slate-800 uppercase leading-tight"><?= esc($st['nama_lengkap']) ?></h3>
                    <p class="text-[10px] font-bold <?= $st['role'] == 'admin' ? 'text-slate-600' : 'text-blue-600' ?> mt-1 uppercase"><?= $roleText ?></p>
                    <div class="w-full border-t border-dashed border-slate-400 my-2"></div>
                    <div class="w-full bg-slate-50 p-1.5 rounded border border-slate-200">
                        <p class="text-[9px] text-slate-500 uppercase font-semibold">Username Login</p>
                        <p class="font-mono font-bold text-slate-800 text-xs tracking-wider"><?= esc($st['username']) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
            el.classList.remove('active-print', 'block');
        });

        const targetTab = document.getElementById('tab-' + tabId);
        targetTab.classList.remove('hidden');
        targetTab.classList.add('active-print', 'block');

        const btnSiswa = document.getElementById('btn-tab-siswa');
        const btnStaff = document.getElementById('btn-tab-staff');

        if (tabId === 'siswa') {
            btnSiswa.className = "px-6 py-3 font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors flex items-center";
            btnStaff.className = "px-6 py-3 font-semibold text-slate-500 hover:text-blue-600 border-b-2 border-transparent transition-colors flex items-center";
        } else {
            btnStaff.className = "px-6 py-3 font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors flex items-center";
            btnSiswa.className = "px-6 py-3 font-semibold text-slate-500 hover:text-blue-600 border-b-2 border-transparent transition-colors flex items-center";
        }
    }

    // ==========================================
    // LOGIKA CETAK SPESIFIK & FILTER
    // ==========================================
    let modeSpesifik = false;
    let kartuTerpilih = [];

    function togglePilihSpesifik() {
        modeSpesifik = !modeSpesifik;

        const btnMode = document.getElementById('btnModePilih');
        const btnCetak = document.getElementById('btnCetak');
        const filterArea = document.getElementById('filterArea');
        const semuaKartu = document.querySelectorAll('.kartu-siswa');

        if (modeSpesifik) {
            // Aktifkan Mode Spesifik
            btnMode.innerHTML = `<span class="mr-2">❌</span> Batal Pilih`;
            btnMode.classList.replace('bg-slate-100', 'bg-red-50');
            btnMode.classList.replace('text-slate-700', 'text-red-600');
            btnMode.classList.replace('border-slate-300', 'border-red-200');

            btnCetak.innerHTML = `<span class="mr-2 text-lg">🖨️</span> Cetak Terpilih (0)`;
            btnCetak.disabled = true;
            btnCetak.classList.add('opacity-50');

            // Sembunyikan panel filter saat mode pilih aktif agar fokus
            filterArea.style.display = 'none';

            semuaKartu.forEach(k => {
                k.classList.add('mode-pilih');
                k.querySelector('.kartu-checkbox').classList.remove('hidden');
            });

            kartuTerpilih = []; // Reset array pilihan
        } else {
            // Matikan Mode Spesifik
            btnMode.innerHTML = `<span class="mr-2">☑️</span> Pilih Kartu Spesifik`;
            btnMode.classList.replace('bg-red-50', 'bg-slate-100');
            btnMode.classList.replace('text-red-600', 'text-slate-700');
            btnMode.classList.replace('border-red-200', 'border-slate-300');

            btnCetak.innerHTML = `<span class="mr-2 text-lg">🖨️</span> Cetak Semua`;
            btnCetak.disabled = false;
            btnCetak.classList.remove('opacity-50');

            filterArea.style.display = 'block';

            semuaKartu.forEach(k => {
                k.classList.remove('mode-pilih', 'dipilih');
                k.querySelector('.kartu-checkbox').classList.add('hidden');
                k.querySelector('input[type="checkbox"]').checked = false;
            });

            kartuTerpilih = [];
        }
    }

    function togglePilihKartu(element) {
        if (!modeSpesifik) return; // Hanya berjalan jika mode pilih aktif

        const id = element.getAttribute('data-id');
        const checkbox = element.querySelector('input[type="checkbox"]');

        if (element.classList.contains('dipilih')) {
            // Uncheck
            element.classList.remove('dipilih');
            checkbox.checked = false;
            kartuTerpilih = kartuTerpilih.filter(item => item !== id);
        } else {
            // Check
            element.classList.add('dipilih');
            checkbox.checked = true;
            kartuTerpilih.push(id);
        }

        // Update tombol cetak
        const btnCetak = document.getElementById('btnCetak');
        btnCetak.innerHTML = `<span class="mr-2 text-lg">🖨️</span> Cetak Terpilih (${kartuTerpilih.length})`;

        if (kartuTerpilih.length > 0) {
            btnCetak.disabled = false;
            btnCetak.classList.remove('opacity-50');
        } else {
            btnCetak.disabled = true;
            btnCetak.classList.add('opacity-50');
        }
    }

    function prosesCetak() {
        if (modeSpesifik) {
            if (kartuTerpilih.length === 0) {
                alert('Pilih minimal satu kartu untuk dicetak!');
                return;
            }

            // Tambahkan window. pada URL agar ekstensi PHP VS Code tidak error
            const url = new window.URL(window.location.href);
            url.searchParams.set('cetak_ids', kartuTerpilih.join(','));
            window.location.href = url.href;
        } else {
            window.print();
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        // Tambahkan window. pada URLSearchParams
        const urlParams = new window.URLSearchParams(window.location.search);
        if (urlParams.has('cetak_ids')) {
            const filterArea = document.getElementById('filterArea');
            if (filterArea) {
                filterArea.style.display = 'block';
                filterArea.innerHTML = `
                    <div class="bg-blue-50 text-blue-700 p-3 rounded-lg border border-blue-200 flex flex-col md:flex-row justify-between items-center gap-3">
                        <span class="font-bold flex items-center"><span class="text-xl mr-2">ℹ️</span> Sedang menampilkan kartu spesifik.</span>
                        <a href="<?= base_url('panel/cetak-kartu') ?>" class="bg-white border border-blue-300 text-blue-700 font-bold px-4 py-2 rounded-lg text-sm hover:bg-blue-100 transition shadow-sm w-full md:w-auto text-center">Tampilkan Semua Data</a>
                    </div>
                `;
            }

            setTimeout(() => {
                window.print();
            }, 800);
        }
    });
</script>
<?= $this->endSection() ?>