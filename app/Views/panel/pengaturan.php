<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800">Pengaturan Sistem</h2>
    <p class="text-slate-500 text-sm mt-1">Kelola identitas institusi, preferensi akademik, dan parameter keamanan ujian.</p>
</div>

<form action="/panel/pengaturan/update" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <?= csrf_field() ?>

    <div class="xl:col-span-2 space-y-6">

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                <h3 class="font-bold text-slate-700">Identitas Sekolah</h3>
            </div>

            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Instansi / Sekolah</label>
                    <input type="text" name="nama_sekolah" value="<?= esc($pengaturan['nama_sekolah'] ?? '') ?>" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none uppercase font-bold text-slate-800 shadow-sm transition">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Kepala Sekolah</label>
                        <input type="text" name="kepala_sekolah" value="<?= esc($pengaturan['kepala_sekolah'] ?? '') ?>" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none shadow-sm transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">NIP Kepala Sekolah</label>
                        <input type="text" name="nip_kepala_sekolah" value="<?= esc($pengaturan['nip_kepala_sekolah'] ?? '') ?>" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm shadow-sm transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kontak (Email / Telepon)</label>
                        <input type="text" name="email_telepon" value="<?= esc($pengaturan['email_telepon'] ?? '') ?>" placeholder="Misal: info@sekolah.com / 021-12345"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm shadow-sm transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                        <textarea name="alamat_sekolah" rows="2" placeholder="Masukkan alamat lengkap instansi..."
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm shadow-sm transition"><?= esc($pengaturan['alamat_sekolah'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                <h3 class="font-bold text-slate-700">Akademik & Kontrol Keamanan</h3>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" value="<?= esc($pengaturan['tahun_ajaran'] ?? '') ?>" placeholder="Misal: 2025/2026" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-sm shadow-sm transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Semester Aktif</label>
                        <select name="semester" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm shadow-sm transition">
                            <option value="ganjil" <?= ($pengaturan['semester'] ?? '') === 'ganjil' ? 'selected' : '' ?>>Ganjil</option>
                            <option value="genap" <?= ($pengaturan['semester'] ?? '') === 'genap' ? 'selected' : '' ?>>Genap</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Zona Waktu Server</label>
                        <select name="zona_waktu" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm shadow-sm transition">
                            <option value="Asia/Jakarta" <?= ($pengaturan['zona_waktu'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' ?>>WIB (Asia/Jakarta)</option>
                            <option value="Asia/Makassar" <?= ($pengaturan['zona_waktu'] ?? '') === 'Asia/Makassar' ? 'selected' : '' ?>>WITA (Asia/Makassar)</option>
                            <option value="Asia/Jayapura" <?= ($pengaturan['zona_waktu'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' ?>>WIT (Asia/Jayapura)</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-center space-x-3 p-4 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition shadow-sm">
                        <input type="checkbox" name="block_multi_login" value="1" <?= ($pengaturan['block_multi_login'] ?? 0) == 1 ? 'checked' : '' ?> class="w-5 h-5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Block Multi Login</p>
                            <p class="text-[10px] text-slate-500 leading-tight mt-0.5">Mencegah siswa masuk di perangkat lain secara bersamaan.</p>
                        </div>
                    </label>

                    <label class="flex items-center space-x-3 p-4 border border-red-200 bg-red-50/50 rounded-xl hover:bg-red-50 cursor-pointer transition shadow-sm">
                        <input type="checkbox" name="maintenance_mode" value="1" <?= ($pengaturan['maintenance_mode'] ?? 0) == 1 ? 'checked' : '' ?> class="w-5 h-5 text-red-600 rounded border-red-300 focus:ring-red-500">
                        <div>
                            <p class="text-sm font-bold text-red-700">Maintenance Mode</p>
                            <p class="text-[10px] text-red-500 leading-tight mt-0.5">Tutup akses login sistem sementara (Kecuali Administrator).</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

    </div>

    <div class="space-y-6">

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden sticky top-6">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                <h3 class="font-bold text-slate-700">Logo Instansi</h3>
            </div>

            <div class="p-6">
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih File Gambar</label>
                    <input type="file" name="logo" accept="image/*"
                        class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-lg p-1 transition">
                    <p class="text-[10px] text-slate-400 mt-2 italic">* Disarankan format transparan (PNG) rasio 1:1.</p>
                </div>

                <div class="border border-slate-200 rounded-xl p-6 flex flex-col items-center justify-center bg-slate-50/50 min-h-[220px]">
                    <?php if (!empty($pengaturan['logo'])): ?>
                        <img src="<?= base_url('uploads/' . $pengaturan['logo']) ?>" alt="Logo" class="w-32 h-32 object-contain drop-shadow-md">
                    <?php else: ?>
                        <div class="w-32 h-32 rounded-full bg-white border-4 border-slate-200 shadow-sm flex items-center justify-center flex-col">
                            <span class="text-4xl opacity-50">🏫</span>
                            <span class="text-[9px] text-slate-400 font-bold mt-2 tracking-wider">NO LOGO</span>
                        </div>
                    <?php endif; ?>

                    <p class="mt-6 text-center font-black text-slate-800 text-sm uppercase px-4 leading-tight tracking-wide">
                        <?= esc($pengaturan['nama_sekolah'] ?? 'NAMA SEKOLAH') ?>
                    </p>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-200">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all flex items-center justify-center gap-2">
                        <span class="text-xl">💾</span> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

    </div>

</form>

<?= $this->endSection() ?>