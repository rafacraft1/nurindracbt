<?php

/**
 * @var array $siswa
 * @var array $staff
 * @var array|null $pengaturan
 */
?>
<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<style>
    /* ===================================================
       STYLE UMUM (Sama untuk Layar & Printer)
       =================================================== */
    .grid-siswa {
        display: grid;
        grid-template-columns: repeat(2, 85.6mm);
        /* 2 Kolom ukuran KTP */
        grid-auto-rows: 43.98mm;
        /* Tinggi kartu siswa */
        gap: 5mm;
        justify-content: center;
        margin: 0 auto;
    }

    /* Responsif di browser jika layar hp/kecil agar tidak overflow */
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
        /* slate-800 */
        border-radius: 0.5rem;
        /* rounded-lg */
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }

    /* ===================================================
       OPTIMASI LAZY LOAD DOM (Hanya untuk Layar/Browser)
       =================================================== */
    @media screen {

        /* Hanya render elemen saat masuk ke viewport (layar) */
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
            /* Jarak cetak 3 cm dari ujung kertas atas */
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

        /* Sembunyikan Elemen UI */
        aside,
        header,
        .no-print {
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

        /* Rule pembagi halaman per 10 kartu */
        .page-container {
            page-break-after: always !important;
            break-after: page !important;
        }

        .page-container:last-child {
            page-break-after: avoid !important;
            break-after: avoid !important;
        }

        /* Paksa grid tetap 2 kolom saat cetak */
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
        }

        /* GRID KHUSUS STAFF */
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
        <p class="text-slate-500 text-sm mt-1">Cetak kredensial login untuk Siswa dan ID Card untuk Staff.</p>
    </div>

    <button onclick="window.print()" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow-lg shadow-blue-500/30 flex items-center justify-center">
        <span class="mr-2 text-lg">🖨️</span> Cetak Halaman Ini
    </button>
</div>

<div class="flex space-x-2 border-b border-slate-300 mb-6 no-print">
    <button onclick="switchTab('siswa')" id="btn-tab-siswa" class="px-6 py-3 font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
        👨‍🎓 Kartu Siswa
    </button>
    <button onclick="switchTab('staff')" id="btn-tab-staff" class="px-6 py-3 font-semibold text-slate-500 hover:text-blue-600 border-b-2 border-transparent transition-colors">
        👨‍🏫 Kartu Staff / Pengawas
    </button>
</div>

<div id="tab-siswa" class="tab-content active-print block">
    <?php if (!empty($siswa)): ?>
        <?php foreach (array_chunk($siswa, 10) as $index => $chunk): ?>
            <div class="page-container <?= $index > 0 ? 'mt-10 print:mt-0' : '' ?>">
                <div class="grid-siswa">
                    <?php foreach ($chunk as $s): ?>
                        <div class="kartu-siswa">
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
        <div class="col-span-full p-8 text-center text-slate-500 no-print border-2 border-dashed border-slate-300 rounded-xl">
            Belum ada data siswa untuk dicetak.
        </div>
    <?php endif; ?>
</div>

<div id="tab-staff" class="tab-content hidden">
    <div class="print-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

        <?php foreach ($staff as $st): ?>
            <div class="kartu-item border-2 border-slate-800 rounded-xl overflow-hidden bg-white relative text-center">

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
            btnSiswa.className = "px-6 py-3 font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors";
            btnStaff.className = "px-6 py-3 font-semibold text-slate-500 hover:text-blue-600 border-b-2 border-transparent transition-colors";
        } else {
            btnStaff.className = "px-6 py-3 font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors";
            btnSiswa.className = "px-6 py-3 font-semibold text-slate-500 hover:text-blue-600 border-b-2 border-transparent transition-colors";
        }
    }
</script>
<?= $this->endSection() ?>