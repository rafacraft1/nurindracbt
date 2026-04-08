<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<style>
    /* CSS Khusus Printer (Menghilangkan elemen UI dan menata kertas A4) */
    @media print {
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            background-color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Sembunyikan Sidebar & Header dari layout utama */
        aside,
        header,
        .no-print {
            display: none !important;
        }

        /* Buka kunci scroll main area agar bisa berhalaman-halaman */
        main {
            overflow: visible !important;
            height: auto !important;
            background: white !important;
            padding: 0 !important;
        }

        /* Pastikan tab yang tidak aktif benar-benar hilang saat diprint */
        .tab-content:not(.active-print) {
            display: none !important;
        }

        /* Mencegah kartu terpotong di tengah halaman */
        .kartu-item {
            page-break-inside: avoid;
        }

        /* Paksa grid menjadi 2 kolom di kertas A4 */
        .print-grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 0.5rem !important;
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
    <div class="print-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4">

        <?php foreach ($siswa as $s): ?>
            <div class="kartu-item border-2 border-slate-800 rounded-lg p-3 bg-white relative">

                <div class="flex items-center border-b-2 border-slate-800 pb-2 mb-2">
                    <div class="w-10 h-10 border border-slate-400 flex items-center justify-center mr-3 rounded-full shrink-0">
                        <span class="text-[8px] text-slate-500">LOGO</span>
                    </div>
                    <div class="leading-tight">
                        <h2 class="font-bold text-sm uppercase text-slate-800">Kartu Login CBT</h2>
                        <p class="text-[10px] text-slate-600">Ujian Berbasis Komputer & Smartphone</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-16 h-20 border border-slate-400 bg-slate-100 flex flex-col items-center justify-center shrink-0">
                        <span class="text-[9px] text-slate-400 font-semibold">3 x 4</span>
                    </div>

                    <div class="flex-1 text-[11px] text-slate-800">
                        <table class="w-full">
                            <tr>
                                <td class="font-bold py-0.5 w-16">Nama</td>
                                <td class="w-2">:</td>
                                <td class="font-bold truncate max-w-[120px] uppercase"><?= esc($s['nama_lengkap']) ?></td>
                            </tr>
                            <tr>
                                <td class="font-bold py-0.5">Kelas</td>
                                <td>:</td>
                                <td><?= esc($s['tingkat'] . ' ' . $s['jurusan'] . ' ' . $s['rombel']) ?></td>
                            </tr>
                            <tr>
                                <td class="font-bold py-0.5">Ruangan</td>
                                <td>:</td>
                                <td class="font-bold text-blue-700 bg-blue-50 px-1 rounded inline-block">
                                    <?= esc($s['nama_ruangan'] ?? 'Belum Diplot') ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="py-0.5">
                                    <div class="border-t border-dashed border-slate-400 my-0.5"></div>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-bold py-0.5 text-blue-700">NISN</td>
                                <td class="font-bold text-blue-700">:</td>
                                <td class="font-mono font-bold text-blue-700 text-xs"><?= esc($s['nisn']) ?></td>
                            </tr>
                            <tr>
                                <td class="font-bold py-0.5 text-red-600">Password</td>
                                <td class="font-bold text-red-600">:</td>
                                <td class="font-mono font-bold text-red-600 text-xs">siswa123 <span class="text-[8px] text-slate-400 font-normal">(Default)</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>

        <?php if (empty($siswa)): ?>
            <div class="col-span-full p-8 text-center text-slate-500 no-print border-2 border-dashed border-slate-300 rounded-xl">
                Belum ada data siswa untuk dicetak.
            </div>
        <?php endif; ?>
    </div>
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
                <div class="<?= $bgClass ?> text-white p-3 border-b-2 border-slate-800">
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

        // 2. Tampilkan tab yang dipilih (tambah class active-print agar CSS @media print tahu mana yang harus diprint)
        const targetTab = document.getElementById('tab-' + tabId);
        targetTab.classList.remove('hidden');
        targetTab.classList.add('active-print', 'block');

        // 3. Update styling tombol tab
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