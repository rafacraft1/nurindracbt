<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-200 pb-5">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Master Jenis Ujian</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola daftar jenis ujian seperti Penilaian Harian, PTS, PAS, dll.</p>
    </div>

    <button onclick="bukaModalJenisUjian()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow flex items-center justify-center w-full md:w-auto">
        <span class="mr-2">➕</span> Tambah Jenis Ujian
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-4xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-100 text-slate-800 font-semibold uppercase text-[11px] tracking-wider">
                <tr>
                    <th class="px-6 py-4 w-16 text-center">No</th>
                    <th class="px-6 py-4">Nama Jenis Ujian</th>
                    <th class="px-6 py-4 text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $no = 1;
                foreach ($jenis_ujian as $ju): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-center font-medium"><?= $no++ ?></td>

                        <td class="px-6 py-4 font-bold text-slate-800 uppercase">
                            <?= esc($ju['nama_ujian']) ?>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick='editJenisUjian(<?= json_encode($ju) ?>)' class="p-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded transition border border-amber-200" title="Edit Data">
                                    ✏️
                                </button>

                                <form action="/panel/jenis-ujian/delete/<?= $ju['id'] ?>" method="POST" id="formDelete<?= $ju['id'] ?>" class="inline-block">
                                    <?= csrf_field() ?>
                                    <button type="button" onclick="konfirmasiHapus(<?= $ju['id'] ?>)" class="p-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded transition border border-red-200" title="Hapus Data">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($jenis_ujian)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-slate-500 border-dashed border-2 m-4 bg-slate-50">
                            Belum ada data jenis ujian.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalJenisUjian" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform" id="modalJenisUjianContent">
        <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white" id="modalTitle">Tambah Jenis Ujian</h3>
            <button type="button" onclick="tutupModalJenisUjian()" class="text-slate-400 hover:text-white">✖</button>
        </div>

        <form action="/panel/jenis-ujian/store" method="POST" id="formJenisUjian">
            <?= csrf_field() ?>
            <div class="p-6">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Jenis Ujian</label>
                <input type="text" name="nama_ujian" id="inputNamaUjian" placeholder="Contoh: PENILAIAN TENGAH SEMESTER" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none uppercase font-bold text-slate-700">
                <p class="text-[10px] text-slate-500 mt-2">Nama ini akan muncul pada Kartu Ujian dan Laporan Nilai.</p>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t flex justify-end gap-3">
                <button type="button" onclick="tutupModalJenisUjian()" class="px-4 py-2 border rounded-lg text-slate-700 hover:bg-slate-100 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow transition">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const mJU = document.getElementById('modalJenisUjian');
    const cJU = document.getElementById('modalJenisUjianContent');
    const fJU = document.getElementById('formJenisUjian');

    function bukaModalJenisUjian() {
        document.getElementById('modalTitle').innerText = 'Tambah Jenis Ujian';
        fJU.action = '/panel/jenis-ujian/store';
        fJU.reset();
        toggleModal(mJU, cJU, true);
    }

    function editJenisUjian(data) {
        document.getElementById('modalTitle').innerText = 'Edit Jenis Ujian';
        fJU.action = '/panel/jenis-ujian/update/' + data.id;
        document.getElementById('inputNamaUjian').value = data.nama_ujian;
        toggleModal(mJU, cJU, true);
    }

    function tutupModalJenisUjian() {
        toggleModal(mJU, cJU, false);
    }

    function toggleModal(modal, content, isShow) {
        if (isShow) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
            }, 10);
        } else {
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }
    }

    function konfirmasiHapus(id) {
        Swal.fire({
            title: 'Hapus Jenis Ujian?',
            text: "Pastikan data ini tidak sedang dipakai di Jadwal Ujian manapun.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('formDelete' + id).submit();
        });
    }
</script>
<?= $this->endSection() ?>