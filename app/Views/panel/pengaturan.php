<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-200 pb-5">
    <div>
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Pengaturan Sistem</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola identitas institusi, preferensi akademik, dan parameter keamanan ujian.</p>
    </div>
</div>

<form action="/panel/pengaturan/update" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    <?= csrf_field() ?>

    <div class="xl:col-span-2 space-y-8">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="font-bold text-slate-700">Identitas Sekolah</h3>
            </div>

            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Instansi / Sekolah</label>
                    <input type="text" name="nama_sekolah" value="<?= esc($pengaturan['nama_sekolah'] ?? '') ?>" required
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none uppercase font-bold text-slate-800 shadow-sm transition">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Kepala Sekolah</label>
                        <input type="text" name="kepala_sekolah" value="<?= esc($pengaturan['kepala_sekolah'] ?? '') ?>" required
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none shadow-sm transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">NIP Kepala Sekolah</label>
                        <input type="text" name="nip_kepala_sekolah" value="<?= esc($pengaturan['nip_kepala_sekolah'] ?? '') ?>" required
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm shadow-sm transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kontak (Email / Telepon)</label>
                        <input type="text" name="email_telepon" value="<?= esc($pengaturan['email_telepon'] ?? '') ?>" placeholder="Misal: info@sekolah.com / 021-12345"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm shadow-sm transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Lengkap</label>
                        <textarea name="alamat_sekolah" rows="2" placeholder="Masukkan alamat lengkap instansi..."
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm shadow-sm transition"><?= esc($pengaturan['alamat_sekolah'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <h3 class="font-bold text-slate-700">Akademik & Kontrol Keamanan</h3>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" value="<?= esc($pengaturan['tahun_ajaran'] ?? '') ?>" placeholder="Misal: 2025/2026" required
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-sm shadow-sm transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Semester Aktif</label>
                        <select name="semester" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm shadow-sm transition bg-white cursor-pointer">
                            <option value="ganjil" <?= ($pengaturan['semester'] ?? '') === 'ganjil' ? 'selected' : '' ?>>Ganjil</option>
                            <option value="genap" <?= ($pengaturan['semester'] ?? '') === 'genap' ? 'selected' : '' ?>>Genap</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Zona Waktu Server</label>
                        <select name="zona_waktu" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm shadow-sm transition bg-white cursor-pointer">
                            <option value="Asia/Jakarta" <?= ($pengaturan['zona_waktu'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' ?>>WIB (Asia/Jakarta)</option>
                            <option value="Asia/Makassar" <?= ($pengaturan['zona_waktu'] ?? '') === 'Asia/Makassar' ? 'selected' : '' ?>>WITA (Asia/Makassar)</option>
                            <option value="Asia/Jayapura" <?= ($pengaturan['zona_waktu'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' ?>>WIT (Asia/Jayapura)</option>
                        </select>
                    </div>
                </div>

                <div class="pt-5 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <label class="flex items-center space-x-4 p-4 border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-blue-300 cursor-pointer transition shadow-sm group">
                        <input type="checkbox" name="block_multi_login" value="1" <?= ($pengaturan['block_multi_login'] ?? 0) == 1 ? 'checked' : '' ?> class="w-6 h-6 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer">
                        <div>
                            <p class="text-sm font-bold text-slate-800 group-hover:text-blue-700 transition">Block Multi Login</p>
                            <p class="text-xs text-slate-500 leading-tight mt-0.5">Mencegah siswa masuk di perangkat lain secara bersamaan.</p>
                        </div>
                    </label>

                    <label class="flex items-center space-x-4 p-4 border border-red-200 bg-red-50/50 rounded-xl hover:bg-red-50 hover:border-red-300 cursor-pointer transition shadow-sm group">
                        <input type="checkbox" name="maintenance_mode" value="1" <?= ($pengaturan['maintenance_mode'] ?? 0) == 1 ? 'checked' : '' ?> class="w-6 h-6 text-red-600 rounded border-red-300 focus:ring-red-500 cursor-pointer">
                        <div>
                            <p class="text-sm font-bold text-red-700 group-hover:text-red-800 transition">Maintenance Mode</p>
                            <p class="text-xs text-red-500/80 leading-tight mt-0.5">Tutup akses login sistem sementara (Kecuali Admin).</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

    </div>

    <div class="space-y-6">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden sticky top-6">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="font-bold text-slate-700">Logo Instansi</h3>
            </div>

            <div class="p-6">
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih File Gambar Baru</label>
                    <input type="file" name="logo" id="inputLogo" accept="image/png, image/jpeg, image/jpg" onchange="previewLogo(event)"
                        class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-xl p-1 transition shadow-sm">
                    <p class="text-[11px] text-slate-400 mt-2 font-medium">PNG/JPG Max 2MB. Rasio 1:1 disarankan.</p>
                </div>

                <div class="border border-slate-200 rounded-2xl p-6 flex flex-col items-center justify-center bg-slate-50/50 min-h-[240px] relative overflow-hidden group">
                    <img id="logoPreview" src="<?= !empty($pengaturan['logo']) ? base_url('uploads/' . $pengaturan['logo']) : '' ?>"
                        alt="Logo Preview"
                        class="w-36 h-36 object-contain drop-shadow-md z-10 <?= empty($pengaturan['logo']) ? 'hidden' : '' ?> transition-transform duration-300 group-hover:scale-110">

                    <div id="noLogoContainer" class="flex flex-col items-center justify-center z-10 <?= !empty($pengaturan['logo']) ? 'hidden' : '' ?>">
                        <div class="w-36 h-36 rounded-full bg-white border-4 border-dashed border-slate-300 shadow-sm flex items-center justify-center flex-col text-slate-300">
                            <svg class="w-12 h-12 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="text-[10px] font-bold tracking-wider">NO LOGO</span>
                        </div>
                    </div>

                    <div class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent z-0 opacity-50"></div>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-200">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

    </div>

</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function previewLogo(event) {
        const file = event.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire('File Terlalu Besar', 'Ukuran maksimal gambar adalah 2MB.', 'error');
                event.target.value = "";
                return;
            }

            const reader = new window.FileReader();
            reader.onload = function() {
                const imgElement = document.getElementById('logoPreview');
                const noLogo = document.getElementById('noLogoContainer');

                imgElement.src = reader.result;
                imgElement.classList.remove('hidden');

                if (noLogo) {
                    noLogo.classList.add('hidden');
                }
            }
            reader.readAsDataURL(file);
        }
    }
</script>
<?= $this->endSection() ?>