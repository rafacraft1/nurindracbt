<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Backup & Restore</h2>
        <p class="text-gray-600">Amankan data sistem atau pulihkan dari cadangan sebelumnya.</p>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p class="font-bold">Sukses!</p>
            <p><?= session()->getFlashdata('success') ?></p>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p class="font-bold">Gagal!</p>
            <p><?= session()->getFlashdata('error') ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="bg-blue-600 text-white px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Backup
                    </h3>
                </div>

                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-6">Pilih modul data yang ingin Anda unduh. Proses ini akan menghasilkan file berekstensi <strong>.zip</strong>.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow duration-200">
                            <h4 class="font-bold text-gray-800 text-md mb-1">Staff & Konfigurasi</h4>
                            <p class="text-xs text-gray-500 mb-4 h-8">Backup data admin, guru, panitia, profil sekolah, dan pengaturan sistem.</p>
                            <a href="/panel/backup-restore/download/staff" class="block text-center w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 border border-gray-300 rounded shadow-sm transition-colors text-sm">
                                Backup Staff
                            </a>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow duration-200">
                            <h4 class="font-bold text-gray-800 text-md mb-1">Data Siswa</h4>
                            <p class="text-xs text-gray-500 mb-4 h-8">Backup data siswa, penempatan ruangan, dan hasil nilai pengerjaan ujian.</p>
                            <a href="/panel/backup-restore/download/siswa" class="block text-center w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 border border-gray-300 rounded shadow-sm transition-colors text-sm">
                                Backup Siswa
                            </a>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow duration-200">
                            <h4 class="font-bold text-gray-800 text-md mb-1">Bank Soal & Ujian</h4>
                            <p class="text-xs text-gray-500 mb-4 h-8">Backup master soal, mata pelajaran, jadwal, serta file audio/gambar soal.</p>
                            <a href="/panel/backup-restore/download/soal" class="block text-center w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 border border-gray-300 rounded shadow-sm transition-colors text-sm">
                                Backup Soal
                            </a>
                        </div>

                        <div class="border border-blue-200 bg-blue-50 rounded-lg p-4 hover:shadow-lg transition-shadow duration-200">
                            <h4 class="font-bold text-blue-800 text-md mb-1">Full Backup (Keseluruhan)</h4>
                            <p class="text-xs text-blue-600 mb-4 h-8">Backup seluruh database beserta seluruh file media CBT PRO.</p>
                            <a href="/panel/backup-restore/download/full" class="block text-center w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition-colors text-sm">
                                Download Full Backup
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden h-full">
                <div class="bg-red-600 text-white px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Restore Sistem
                    </h3>
                </div>

                <div class="p-6">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-6">
                        <p class="text-xs text-yellow-700 text-justify">
                            <strong>PERHATIAN BACA SEBELUM RESTORE!</strong><br>
                            Melakukan restore akan menghapus data <em>existing</em> pada tabel terkait dan menggantinya dengan data dari file backup. Pastikan tidak ada peserta yang sedang melaksanakan ujian!
                        </p>
                    </div>

                    <form action="/panel/backup-restore/restore" method="POST" enctype="multipart/form-data" id="formRestore" onsubmit="return confirmRestore()">
                        <?= csrf_field() ?>

                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Backup (.zip)</label>
                            <input type="file" name="file_backup" accept=".zip" required
                                class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded file:border-0
                                file:text-sm file:font-semibold
                                file:bg-red-50 file:text-red-700
                                hover:file:bg-red-100 cursor-pointer border border-gray-300 rounded p-1">
                        </div>

                        <button type="submit" id="btnRestore" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out flex justify-center items-center">
                            <span id="btnText">Jalankan Restore Data</span>
                            <svg id="btnSpinner" class="animate-spin hidden ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <div class="mt-8 bg-white rounded-lg shadow-md border border-red-300 overflow-hidden">
        <div class="bg-gray-800 text-white px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-bold flex items-center">
                <span class="mr-2">⚠️</span> Zona Bahaya (Factory Reset)
            </h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">Proses ini akan <strong>MENGHAPUS PERMANEN</strong> seluruh data siswa, nilai, bank soal, jadwal, dan guru. Sistem akan kembali kosong seperti baru diinstal. Akun Anda (Super Admin) akan tetap dipertahankan.</p>

            <form action="/panel/backup-restore/factory-reset" method="POST" id="formFactoryReset">
                <?= csrf_field() ?>
                <button type="button" onclick="confirmFactoryReset()" class="bg-gray-800 hover:bg-black text-white font-bold py-2 px-6 rounded shadow transition-colors text-sm">
                    ⚠️ HAPUS SEMUA DATA (RESET SISTEM)
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    // Fungsi Konfirmasi Restore (Javascript Bawaan)
    function confirmRestore() {
        const isConfirmed = confirm('APAKAH ANDA YAKIN?\n\nData sistem saat ini akan ditimpa dengan data dari file backup. Proses ini tidak dapat dibatalkan.');

        if (isConfirmed) {
            const btn = document.getElementById('btnRestore');
            const text = document.getElementById('btnText');
            const spinner = document.getElementById('btnSpinner');

            btn.classList.remove('bg-red-600', 'hover:bg-red-700');
            btn.classList.add('bg-gray-400', 'cursor-not-allowed');
            btn.disabled = true;

            text.innerText = "Memproses Restore...";
            spinner.classList.remove('hidden');

            return true;
        }

        return false;
    }

    // Fungsi Konfirmasi Factory Reset Menggunakan SweetAlert2
    function confirmFactoryReset() {
        Swal.fire({
            title: 'PERINGATAN FINAL!',
            html: "Seluruh data (Siswa, Soal, Jadwal, Nilai) akan dihapus <b>PERMANEN</b>.<br>Sistem akan kembali kosong seperti baru diinstal.<br><br>Anda yakin ingin melanjutkan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33', // Warna merah bahaya
            cancelButtonColor: '#64748b', // Warna abu-abu netral
            confirmButtonText: 'Ya, Reset Sistem!',
            cancelButtonText: 'Batal',
            reverseButtons: true // Memindah posisi tombol Batal ke sebelah kiri
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika user klik Ya, form disubmit secara paksa via Javascript
                document.getElementById('formFactoryReset').submit();
            }
        });
    }
</script>
<?= $this->endSection() ?>