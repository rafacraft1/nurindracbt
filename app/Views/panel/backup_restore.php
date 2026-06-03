<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>
<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-200 pb-5">
    <div>
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Manajemen Database</h2>
        <p class="text-slate-500 text-sm mt-1">Amankan data sistem CBT atau pulihkan dari cadangan (Backup) sebelumnya.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 text-slate-800 px-6 py-4 border-b border-slate-200 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <h3 class="text-lg font-bold">Download Backup Sistem</h3>
            </div>

            <div class="p-6">
                <p class="text-sm text-slate-500 font-medium mb-6 bg-blue-50 p-3 rounded-lg border border-blue-100">
                    Pilih modul data yang ingin Anda amankan. Proses ini akan mengkompresi data menjadi file berekstensi <strong class="text-blue-700">.zip</strong>.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="border border-slate-200 rounded-xl p-5 hover:shadow-md hover:border-blue-300 transition-all duration-300 group flex flex-col h-full">
                        <h4 class="font-bold text-slate-800 text-base mb-1 group-hover:text-blue-600 transition-colors">Staff & Konfigurasi</h4>
                        <p class="text-xs text-slate-500 mb-5 flex-grow">Backup data admin, guru, panitia, profil sekolah, dan pengaturan sistem.</p>
                        <a href="/panel/backup-restore/download/staff" class="text-center w-full bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-700 font-bold py-2.5 px-4 rounded-lg transition-all text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Backup Staff
                        </a>
                    </div>

                    <div class="border border-slate-200 rounded-xl p-5 hover:shadow-md hover:border-emerald-300 transition-all duration-300 group flex flex-col h-full">
                        <h4 class="font-bold text-slate-800 text-base mb-1 group-hover:text-emerald-600 transition-colors">Data Siswa</h4>
                        <p class="text-xs text-slate-500 mb-5 flex-grow">Backup data siswa, penempatan ruangan, dan hasil nilai ujian.</p>
                        <a href="/panel/backup-restore/download/siswa" class="text-center w-full bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-700 font-bold py-2.5 px-4 rounded-lg transition-all text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Backup Siswa
                        </a>
                    </div>

                    <div class="border border-slate-200 rounded-xl p-5 hover:shadow-md hover:border-indigo-300 transition-all duration-300 group flex flex-col h-full">
                        <h4 class="font-bold text-slate-800 text-base mb-1 group-hover:text-indigo-600 transition-colors">Bank Soal & Ujian</h4>
                        <p class="text-xs text-slate-500 mb-5 flex-grow">Backup master soal, jadwal, serta file media gambar/audio.</p>
                        <a href="/panel/backup-restore/download/soal" class="text-center w-full bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 font-bold py-2.5 px-4 rounded-lg transition-all text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Backup Soal
                        </a>
                    </div>

                    <div class="border border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 hover:shadow-lg transition-all duration-300 group flex flex-col h-full relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 opacity-10">
                            <svg class="w-24 h-24 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </div>
                        <h4 class="font-bold text-blue-800 text-base mb-1 z-10">Full Backup (Keseluruhan)</h4>
                        <p class="text-xs text-blue-600/80 mb-5 flex-grow z-10">Backup seluruh database MySQL beserta struktur file media sistem CBT PRO.</p>
                        <a href="/panel/backup-restore/download/full" class="text-center w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md shadow-blue-500/30 transition-all text-sm z-10 flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download Full Backup
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
            <div class="bg-slate-50 text-slate-800 px-6 py-4 border-b border-slate-200 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <h3 class="text-lg font-bold">Restore Sistem</h3>
            </div>

            <div class="p-6 flex-grow">
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 shadow-inner">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="text-xs text-amber-800 font-medium leading-relaxed">
                            <strong class="block mb-1 text-sm">Peringatan Kritis!</strong>
                            Restore akan menimpa/menghapus data saat ini dengan file backup. Pastikan tidak ada peserta ujian yang sedang mengerjakan.
                        </p>
                    </div>
                </div>

                <form action="/panel/backup-restore/restore" method="POST" enctype="multipart/form-data" id="formRestore">
                    <?= csrf_field() ?>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih File Backup (.zip)</label>
                        <input type="file" name="file_backup" accept=".zip" required
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-300 rounded-xl p-1 transition shadow-sm bg-white">
                    </div>

                    <button type="button" onclick="konfirmasiRestore()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-4 rounded-xl transition duration-300 shadow-lg shadow-indigo-500/30 flex justify-center items-center gap-2 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Jalankan Restore Data
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<div class="mt-8 bg-white rounded-2xl shadow-sm border border-red-300 overflow-hidden relative">
    <div class="absolute inset-0 bg-red-50/50 pointer-events-none"></div>
    <div class="bg-red-50 text-red-800 px-6 py-4 border-b border-red-200 flex items-center relative z-10">
        <svg class="w-6 h-6 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <h3 class="text-lg font-black tracking-wide">ZONA BAHAYA (FACTORY RESET MURNI)</h3>
    </div>
    <div class="p-6 relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex-1">
            <p class="text-sm text-red-800 font-medium leading-relaxed">
                Fitur ini akan <strong class="text-red-700">MEMBUMIHANGUSKAN</strong> database dan mengembalikannya ke pengaturan pabrik (Rollback & Remigrate) serta menghapus isi folder media.
            </p>
        </div>

        <div class="w-full md:w-auto shrink-0">
            <input type="hidden" id="tokenCsrf" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

            <button type="button" onclick="konfirmasiFactoryReset()" class="w-full md:w-auto bg-red-600 hover:bg-red-700 text-white font-black py-3.5 px-8 rounded-xl shadow-lg shadow-red-500/30 transition-all text-sm uppercase tracking-widest flex items-center justify-center gap-2 transform hover:scale-105 border-2 border-red-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Reset Pabrik Sistem
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script>
    function konfirmasiRestore() {
        const fileInput = document.querySelector('input[name="file_backup"]');
        if (!fileInput.value) {
            Swal.fire('File Kosong', 'Harap pilih file backup .zip terlebih dahulu.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Eksekusi Restore?',
            html: "Data yang ada saat ini akan ditimpa dengan data dari file backup.<br><br><strong class='text-amber-600'>Pastikan file backup benar dan valid!</strong>",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Jalankan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memulihkan Sistem...',
                    html: 'Jangan tutup atau <i>refresh</i> halaman ini. Proses ini mungkin memakan waktu beberapa menit.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                document.getElementById('formRestore').submit();
            }
        });
    }

    // ==============================================================================
    // ENGINE JAVASCRIPT AJAX UNTUK RESET PABRIK MURNI (TRUE WIPE OUT)
    // Mengamankan siklus Request karena Database akan dihancurkan di tengah jalan
    // ==============================================================================
    function konfirmasiFactoryReset() {
        Swal.fire({
            title: 'PERINGATAN FINAL!',
            html: "Seluruh struktur tabel akan dihancurkan <b>PERMANEN</b>.<br>Sistem akan melakukan <i>Migrate Down</i> dan <i>Seed</i> ulang.<br><br>Untuk melanjutkan, silakan ketik:<br><strong class='text-red-600 tracking-widest text-lg'>HAPUS PERMANEN</strong>",
            icon: 'warning',
            input: 'text',
            inputPlaceholder: 'Ketik tulisan di atas',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'EKSEKUSI RESET!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            inputValidator: (value) => {
                if (value !== 'HAPUS PERMANEN') {
                    return 'Anda harus mengetik "HAPUS PERMANEN" dengan benar dan huruf kapital!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Membumihanguskan Data...',
                    html: 'Sedang menghapus folder, membongkar database, dan menyuntikkan konfigurasi awal.<br><b>Sangat dilarang menutup browser Anda!</b>',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Tembakkan AJAX Request ke Controller tanpa me-reload halaman
                let formData = new window.FormData();
                formData.append('konfirmasi_reset', result.value);
                formData.append('<?= csrf_token() ?>', document.getElementById('tokenCsrf').value);

                fetch('/panel/backup-restore/factory-reset', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Sesuai dengan konfigurasi Seeder Anda
                            Swal.fire({
                                title: 'Reset Pabrik Berhasil!',
                                html: `Sistem telah dikembalikan ke standar awal.<br><br>
                                   <div class="bg-slate-100 p-4 rounded-xl border border-slate-200 mt-2 mb-2 text-left">
                                      <span class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center mb-2"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg> Kredensial Default Admin</span>
                                      <div class="mt-2 text-sm">
                                        <span class="font-medium text-slate-600">Username:</span> <code class="bg-white px-2 py-1 rounded shadow-sm border border-slate-200 text-blue-600 font-bold ml-1">admin</code><br><br>
                                        <span class="font-medium text-slate-600">Password:</span> <code class="bg-white px-2 py-1 rounded shadow-sm border border-slate-200 text-blue-600 font-bold ml-1 pl-2">admin123</code>
                                      </div>
                                   </div>
                                   <p class="text-sm font-medium text-slate-500 italic mt-3">Klik tombol di bawah untuk Logout dan mengakhiri sesi.</p>`,
                                icon: 'success',
                                confirmButtonColor: '#10b981',
                                confirmButtonText: 'OK, Logout Sekarang',
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then(() => {
                                window.location.href = '/logout'; // Pindah ke halaman Login otomatis
                            });
                        } else {
                            Swal.fire('Gagal Mereset Sistem', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Koneksi Terputus', 'Terjadi kesalahan sistem atau server timeout.', 'error');
                    });
            }
        });
    }

    <?php if (session()->getFlashdata('error')) : ?>
        Toastify({
            text: "<?= esc(session()->getFlashdata('error'), 'js') ?>",
            duration: 5000,
            style: {
                background: "#ef4444",
                borderRadius: "10px",
                fontWeight: "bold"
            }
        }).showToast();
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')) : ?>
        Toastify({
            text: "<?= esc(session()->getFlashdata('success'), 'js') ?>",
            duration: 4000,
            style: {
                background: "#10b981",
                borderRadius: "10px",
                fontWeight: "bold"
            }
        }).showToast();
    <?php endif; ?>
</script>
<?= $this->endSection() ?>