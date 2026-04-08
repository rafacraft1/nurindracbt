<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Manajemen Data Siswa</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola data peserta ujian dan kelas.</p>
    </div>

    <div class="flex gap-2 w-full lg:w-auto">
        <button onclick="bukaModalSiswa()" class="flex-1 lg:flex-none justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-bold text-sm transition shadow flex items-center">
            <span class="mr-2">➕</span> Tambah Manual
        </button>
        <button onclick="bukaModalImport()" class="flex-1 lg:flex-none justify-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg font-bold text-sm transition shadow flex items-center">
            <span class="mr-2">📁</span> Import Excel
        </button>
    </div>
</div>

<div class="bg-white p-4 rounded-t-xl border border-slate-200 border-b-0 flex justify-between items-center">
    <form action="/panel/siswa" method="GET" class="flex w-full md:w-auto gap-2">
        <input type="text" name="search" value="<?= esc($search) ?>" placeholder="Cari NISN atau Nama..." class="w-full md:w-64 px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
        <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2 rounded-lg text-sm font-semibold transition shadow-sm">Cari</button>
        <?php if (!empty($search)): ?>
            <a href="/panel/siswa" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-semibold transition border border-red-200">Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="bg-white rounded-b-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600 table-fixed min-w-[800px]">
            <thead class="bg-slate-100 text-slate-800 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-center w-14">No</th>
                    <th class="px-4 py-3 w-56">NISN / Akun</th>
                    <th class="px-4 py-3">Nama Lengkap</th>
                    <th class="px-4 py-3 text-center w-40">Kelas</th>
                    <th class="px-4 py-3 text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php
                $no = ($currentPage - 1) * 50 + 1;
                foreach ($siswa as $s):
                ?>
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3 text-center font-medium text-slate-500"><?= $no++ ?></td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-blue-600 text-base"><?= esc($s['nisn']) ?></div>
                            <div class="text-[10px] text-emerald-600 font-mono mt-0.5">🔑 Enkripsi Aktif</div>
                        </td>
                        <td class="px-4 py-3 font-bold text-slate-800 uppercase tracking-wide truncate pr-4">
                            <?= esc($s['nama_lengkap']) ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-slate-100 text-slate-700 px-3 py-1.5 rounded-md text-xs font-bold border border-slate-200 shadow-sm whitespace-nowrap">
                                <?= esc($s['tingkat'] . ' ' . $s['jurusan'] . ' ' . $s['rombel']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick='editSiswa(<?= json_encode($s) ?>)' class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-200 rounded-lg transition shadow-sm" title="Edit Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                                <form action="/panel/siswa/delete/<?= $s['id'] ?>" method="POST" id="formDelete<?= $s['id'] ?>" class="inline-block">
                                    <?= csrf_field() ?>
                                    <button type="button" onclick="konfirmasiHapus(<?= $s['id'] ?>)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition shadow-sm" title="Hapus Siswa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($siswa)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-slate-500 bg-slate-50">
                            <span class="text-4xl block mb-3">🔍</span>
                            <p class="font-semibold text-slate-600">Data Siswa Kosong</p>
                            <p class="text-xs text-slate-400 mt-1">Belum ada data atau pencarian tidak ditemukan.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <span class="text-xs font-semibold text-slate-500">
                Halaman <span class="text-slate-800 bg-white px-2 py-0.5 rounded border border-slate-300"><?= $currentPage ?></span> dari <span class="text-slate-800"><?= $totalPages ?></span>
                <span class="mx-2 text-slate-300">|</span> Total <span class="text-blue-600 font-bold"><?= number_format($totalData, 0, ',', '.') ?></span> Siswa
            </span>

            <div class="flex gap-1.5">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search ?? '') ?>" class="px-3 py-1.5 bg-white border border-slate-300 rounded-md text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-800 font-medium transition shadow-sm">Prev</a>
                <?php endif; ?>

                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);

                for ($i = $startPage; $i <= $endPage; $i++):
                    // Pisahkan logikanya ke dalam variabel agar VS Code tidak bingung
                    $activeClass = ($i == $currentPage)
                        ? 'bg-blue-600 text-white border-blue-600 ring-2 ring-blue-600/20'
                        : 'bg-white border-slate-300 text-slate-600 hover:bg-slate-50';
                ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>" class="px-3 py-1.5 border rounded-md text-sm font-bold transition shadow-sm <?= $activeClass ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search ?? '') ?>" class="px-3 py-1.5 bg-white border border-slate-300 rounded-md text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-800 font-medium transition shadow-sm">Next</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<div id="modalSiswa" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform scale-95 transition-transform" id="modalSiswaContent">
        <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white" id="modalTitle">Tambah Siswa Baru</h3>
            <button type="button" onclick="tutupModalSiswa()" class="text-slate-400 hover:text-white transition">✖</button>
        </div>

        <form action="/panel/siswa/store" method="POST" id="formSiswa">
            <?= csrf_field() ?>
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">NISN (Username)</label>
                        <input type="text" name="nisn" id="inputNisn" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-mono font-bold text-slate-700 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                        <input type="text" name="password" id="inputPassword" placeholder="Kosongkan = siswa123" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="inputNama" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none uppercase font-bold text-slate-700 transition">
                </div>

                <div class="grid grid-cols-3 gap-3 bg-slate-50 p-3 rounded-lg border border-slate-200">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase">Tingkat</label>
                        <input type="text" name="tingkat" id="inputTingkat" placeholder="Misal: XII" required class="w-full px-3 py-1.5 border border-slate-300 rounded-md outline-none uppercase text-sm focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase">Jurusan</label>
                        <input type="text" name="jurusan" id="inputJurusan" placeholder="Misal: RPL" required class="w-full px-3 py-1.5 border border-slate-300 rounded-md outline-none uppercase text-sm focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase">Rombel</label>
                        <input type="text" name="rombel" id="inputRombel" placeholder="Misal: 1 / A" required class="w-full px-3 py-1.5 border border-slate-300 rounded-md outline-none uppercase text-sm focus:border-blue-500 transition">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Plotting Ruangan (Opsional)</label>
                    <select name="ruangan_id" id="inputRuangan" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white font-bold text-indigo-700 outline-none cursor-pointer">
                        <option value="">-- Bebas Ruangan --</option>
                        <?php foreach ($ruangan as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= esc($r['nama_ruangan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">*Ruangan ini hanya acuan, siswa tetap masuk lewat Lobi Ujian berdasarkan Jadwal.</p>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="tutupModalSiswa()" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 font-semibold hover:bg-slate-200 transition">Batal</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-md transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg> Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalImport" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform" id="modalImportContent">
        <div class="bg-emerald-600 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white">Import Data Siswa</h3>
            <button type="button" onclick="tutupModalImport()" class="text-emerald-100 hover:text-white transition">✖</button>
        </div>

        <form action="/panel/siswa/import" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="p-6 space-y-4">
                <div class="bg-amber-50 border border-amber-200 p-3 rounded-lg text-xs text-amber-800 leading-relaxed shadow-inner">
                    Siapkan file Excel (.xlsx) Anda. Urutan kolom wajib seperti ini (Tanpa Header / Baris ke-1 langsung data):<br>
                    <strong class="text-amber-900 mt-2 block font-mono bg-amber-100 p-2 rounded">
                        A: NISN<br>B: Nama Lengkap<br>C: Tingkat (Misal X)<br>D: Jurusan (Misal IPA)<br>E: Rombel (Misal 1)
                    </strong>
                </div>

                <div class="pt-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Upload File (.xlsx)</label>
                    <input type="file" name="file_excel" accept=".xlsx, .xls" required class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-slate-200 rounded-lg">
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="tutupModalImport()" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 font-semibold hover:bg-slate-200 transition">Batal</button>
                <button type="submit" onclick="this.innerHTML='Memproses...'" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-bold shadow-md transition flex items-center">
                    🚀 Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const mSiswa = document.getElementById('modalSiswa');
    const cSiswa = document.getElementById('modalSiswaContent');
    const fSiswa = document.getElementById('formSiswa');

    function bukaModalSiswa() {
        document.getElementById('modalTitle').innerText = 'Tambah Siswa Baru';
        fSiswa.action = '/panel/siswa/store';
        fSiswa.reset();
        toggleModal(mSiswa, cSiswa, true);
    }

    function editSiswa(data) {
        document.getElementById('modalTitle').innerText = 'Edit Data Siswa';
        fSiswa.action = '/panel/siswa/update/' + data.id;

        document.getElementById('inputNisn').value = data.nisn;
        document.getElementById('inputNama').value = data.nama_lengkap;
        document.getElementById('inputTingkat').value = data.tingkat;
        document.getElementById('inputJurusan').value = data.jurusan;
        document.getElementById('inputRombel').value = data.rombel;
        document.getElementById('inputRuangan').value = data.ruangan_id || '';
        document.getElementById('inputPassword').value = '';

        toggleModal(mSiswa, cSiswa, true);
    }

    function tutupModalSiswa() {
        toggleModal(mSiswa, cSiswa, false);
    }

    const mImport = document.getElementById('modalImport');
    const cImport = document.getElementById('modalImportContent');

    function bukaModalImport() {
        toggleModal(mImport, cImport, true);
    }

    function tutupModalImport() {
        toggleModal(mImport, cImport, false);
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
            title: 'Hapus Siswa?',
            text: "Data siswa akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('formDelete' + id).submit();
        })
    }
</script>
<?= $this->endSection() ?>