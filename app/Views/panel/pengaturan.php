<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800">Pengaturan Sistem</h2>
    <p class="text-slate-500 text-sm mt-1">Kelola identitas sekolah untuk Kop Surat, Kartu Ujian, dan Laporan.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
            <h3 class="font-bold text-slate-700">Identitas Sekolah</h3>
        </div>

        <form action="/panel/pengaturan/update" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Instansi / Sekolah</label>
                <input type="text" name="nama_sekolah" value="<?= esc($pengaturan['nama_sekolah']) ?>" required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none uppercase font-bold text-slate-800">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Kepala Sekolah</label>
                    <input type="text" name="kepala_sekolah" value="<?= esc($pengaturan['kepala_sekolah']) ?>" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">NIP Kepala Sekolah</label>
                    <input type="text" name="nip_kepala_sekolah" value="<?= esc($pengaturan['nip_kepala_sekolah']) ?>" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Ganti Logo Sekolah</label>
                <input type="file" name="logo" accept="image/*"
                    class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-lg p-1">
                <p class="text-[10px] text-slate-400 mt-2 italic">* Format JPG/PNG, rekomendasi rasio 1:1.</p>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold shadow-lg shadow-blue-500/30 transition">
                    💾 Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
            <h3 class="font-bold text-slate-700">Pratinjau Logo</h3>
        </div>
        <div class="p-6 flex flex-col items-center justify-center min-h-[250px] bg-slate-50/50">
            <?php if ($pengaturan['logo']): ?>
                <img src="<?= base_url('uploads/' . $pengaturan['logo']) ?>" alt="Logo" class="w-32 h-32 object-contain drop-shadow-md">
            <?php else: ?>
                <div class="w-32 h-32 rounded-full bg-slate-200 border-4 border-white shadow-inner flex items-center justify-center flex-col">
                    <span class="text-3xl">🏫</span>
                    <span class="text-[10px] text-slate-400 font-bold mt-2">NO LOGO</span>
                </div>
            <?php endif; ?>
            <p class="mt-6 text-center font-black text-slate-800 text-sm uppercase px-4 leading-tight">
                <?= esc($pengaturan['nama_sekolah']) ?>
            </p>
        </div>
    </div>

</div>

<?= $this->endSection() ?>