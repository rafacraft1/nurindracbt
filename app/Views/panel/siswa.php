<?php

/**
 * @var array  $siswa
 * @var string $search
 * @var int    $currentPage
 * @var int    $totalPages
 * @var int    $totalData
 * @var array  $ruangan
 * @var string $sortCol
 * @var string $sortDir
 */
?>
<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<style>
    .progress-striped {
        background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.25) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.25) 50%, rgba(255, 255, 255, 0.25) 75%, transparent 75%, transparent);
        background-size: 1.5rem 1.5rem;
    }

    .animate-stripes {
        animation: progress-stripes 1s linear infinite;
    }

    @keyframes progress-stripes {
        from {
            background-position: 1.5rem 0;
        }

        to {
            background-position: 0 0;
        }
    }
</style>

<div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Manajemen Data Siswa</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola data peserta ujian dan kelas.</p>
    </div>

    <div class="flex flex-wrap gap-2 w-full lg:w-auto">
        <button id="btnBulkDelete" onclick="konfirmasiHapusBatch()" class="hidden flex-1 lg:flex-none justify-center bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-bold text-sm transition shadow items-center animate-pulse">
            <span class="mr-2">🗑️</span> Hapus Terpilih (<span id="countSelected">0</span>)
        </button>

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
        <input type="hidden" name="sort" value="<?= esc($sortCol) ?>">
        <input type="hidden" name="dir" value="<?= esc($sortDir) ?>">

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
                    <th class="px-4 py-3 text-center w-12">
                        <input type="checkbox" id="checkAll" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer transition">
                    </th>
                    <th class="px-4 py-3 text-center w-14">No</th>

                    <?php $dirNisn = ($sortCol == 'nisn' && $sortDir == 'ASC') ? 'DESC' : 'ASC'; ?>
                    <th class="px-4 py-3 w-56 cursor-pointer hover:bg-slate-200 transition select-none" onclick="window.location='?page=1&search=<?= urlencode($search) ?>&sort=nisn&dir=<?= $dirNisn ?>'" title="Urutkan berdasarkan NISN">
                        <div class="flex items-center justify-between">
                            <span>NISN / Akun</span>
                            <?php if ($sortCol == 'nisn'): ?>
                                <span class="text-blue-600 text-xs"><?= $sortDir == 'ASC' ? '▲' : '▼' ?></span>
                            <?php else: ?>
                                <span class="text-slate-300 text-xs">↕</span>
                            <?php endif; ?>
                        </div>
                    </th>

                    <?php $dirNama = ($sortCol == 'nama' && $sortDir == 'ASC') ? 'DESC' : 'ASC'; ?>
                    <th class="px-4 py-3 cursor-pointer hover:bg-slate-200 transition select-none" onclick="window.location='?page=1&search=<?= urlencode($search) ?>&sort=nama&dir=<?= $dirNama ?>'" title="Urutkan berdasarkan Nama">
                        <div class="flex items-center justify-between">
                            <span>Nama Lengkap</span>
                            <?php if ($sortCol == 'nama'): ?>
                                <span class="text-blue-600 text-xs"><?= $sortDir == 'ASC' ? '▲' : '▼' ?></span>
                            <?php else: ?>
                                <span class="text-slate-300 text-xs">↕</span>
                            <?php endif; ?>
                        </div>
                    </th>

                    <?php $dirKelas = ($sortCol == 'kelas' && $sortDir == 'ASC') ? 'DESC' : 'ASC'; ?>
                    <th class="px-4 py-3 text-center w-40 cursor-pointer hover:bg-slate-200 transition select-none" onclick="window.location='?page=1&search=<?= urlencode($search) ?>&sort=kelas&dir=<?= $dirKelas ?>'" title="Urutkan berdasarkan Kelas">
                        <div class="flex items-center justify-center gap-2">
                            <span>Kelas</span>
                            <?php if ($sortCol == 'kelas'): ?>
                                <span class="text-blue-600 text-xs"><?= $sortDir == 'ASC' ? '▲' : '▼' ?></span>
                            <?php else: ?>
                                <span class="text-slate-300 text-xs">↕</span>
                            <?php endif; ?>
                        </div>
                    </th>

                    <th class="px-4 py-3 text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="tableSiswaBody">
                <?php
                $no = ($currentPage - 1) * 50 + 1;
                foreach ($siswa as $s):
                ?>
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" value="<?= $s['id'] ?>" class="checkItem w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer transition">
                        </td>
                        <td class="px-4 py-3 text-center font-medium text-slate-500"><?= $no++ ?></td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-blue-600 text-base"><?= esc($s['nisn']) ?></div>
                            <div class="text-[10px] text-emerald-600 font-mono mt-0.5">🔑 <?= esc($s['password_plain'] ?? 'siswa123') ?></div>
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
                        <td colspan="6" class="px-6 py-16 text-center text-slate-500 bg-slate-50">
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
                    <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search ?? '') ?>&sort=<?= $sortCol ?>&dir=<?= $sortDir ?>" class="px-3 py-1.5 bg-white border border-slate-300 rounded-md text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-800 font-medium transition shadow-sm">Prev</a>
                <?php endif; ?>

                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);

                for ($i = $startPage; $i <= $endPage; $i++):
                    $activeClass = ($i == $currentPage)
                        ? 'bg-blue-600 text-white border-blue-600 ring-2 ring-blue-600/20'
                        : 'bg-white border-slate-300 text-slate-600 hover:bg-slate-50';
                ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&sort=<?= $sortCol ?>&dir=<?= $sortDir ?>" class="px-3 py-1.5 border rounded-md text-sm font-bold transition shadow-sm <?= $activeClass ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search ?? '') ?>&sort=<?= $sortCol ?>&dir=<?= $sortDir ?>" class="px-3 py-1.5 bg-white border border-slate-300 rounded-md text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-800 font-medium transition shadow-sm">Next</a>
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

        <form action="/panel/siswa/import" method="POST" enctype="multipart/form-data" id="formImportSiswa">
            <?= csrf_field() ?>
            <div class="p-6 space-y-4">
                <div class="bg-amber-50 border border-amber-200 p-3 rounded-lg text-xs text-amber-800 leading-relaxed shadow-inner">
                    Siapkan file Excel (.xlsx) Anda. Urutan kolom wajib seperti ini (Tanpa Header / Baris ke-1 langsung data):<br>
                    <strong class="text-amber-900 mt-2 block font-mono bg-amber-100 p-2 rounded leading-loose">
                        A: NISN<br>B: Nama Lengkap<br>C: Tingkat (Misal X)<br>D: Jurusan (Misal IPA)<br>E: Rombel (Misal 1)<br>F: Password <span class="text-[10px] text-amber-600 font-sans font-bold">(Opsional, default siswa123)</span>
                    </strong>
                </div>

                <div class="pt-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Upload File (.xlsx)</label>
                    <input type="file" name="file_excel" accept=".xlsx, .xls" required class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-slate-200 rounded-lg">
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="tutupModalImport()" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 font-semibold hover:bg-slate-200 transition">Batal</button>
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-bold shadow-md transition flex items-center">
                    🚀 Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // ==========================================
    // MANAJEMEN CHECKBOX (PERSISTENT LINTAS PAGE)
    // ==========================================
    let selectedIds = JSON.parse(sessionStorage.getItem('selectedSiswaIds')) || [];

    const checkAll = document.getElementById('checkAll');
    const checkItems = document.querySelectorAll('.checkItem');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const countSelected = document.getElementById('countSelected');

    function saveCheckboxState() {
        sessionStorage.setItem('selectedSiswaIds', JSON.stringify(selectedIds));
        updateBulkDeleteUI();
    }

    function updateBulkDeleteUI() {
        countSelected.innerText = selectedIds.length;

        if (selectedIds.length > 0) {
            btnBulkDelete.classList.remove('hidden');
            btnBulkDelete.classList.add('flex');
        } else {
            btnBulkDelete.classList.add('hidden');
            btnBulkDelete.classList.remove('flex');
        }

        // Cek apakah semua item di halaman INI tercentang
        if (checkItems.length > 0) {
            const allCheckedOnPage = Array.from(checkItems).every(item => item.checked);
            checkAll.checked = allCheckedOnPage;
        }
    }

    // Inisialisasi saat DOM dimuat
    checkItems.forEach(item => {
        // Restore status centang jika ID ada di array session
        if (selectedIds.includes(item.value)) {
            item.checked = true;
        }

        // Listener jika di klik manual per item
        item.addEventListener('change', function() {
            if (this.checked) {
                if (!selectedIds.includes(this.value)) selectedIds.push(this.value);
            } else {
                selectedIds = selectedIds.filter(id => id !== this.value);
            }
            saveCheckboxState();
        });
    });

    // Panggil update UI pertama kali halaman diload
    updateBulkDeleteUI();

    // Listener Check All (Hanya mempengaruhi yang tampil di DOM / Halaman Saat Ini)
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            const isChecked = this.checked;

            checkItems.forEach(item => {
                item.checked = isChecked;
                if (isChecked) {
                    if (!selectedIds.includes(item.value)) selectedIds.push(item.value);
                } else {
                    selectedIds = selectedIds.filter(id => id !== item.value);
                }
            });
            saveCheckboxState();
        });
    }

    function konfirmasiHapusBatch() {
        if (selectedIds.length === 0) return;

        Swal.fire({
            title: `Hapus ${selectedIds.length} Siswa?`,
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus Semua!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    let formData = new window.FormData();
                    selectedIds.forEach(id => formData.append('ids[]', id));

                    let res = await fetch('/panel/siswa/delete-batch', {
                        method: 'POST',
                        body: formData
                    }).then(r => r.json());

                    if (res.csrf) {
                        document.querySelector('meta[name="csrf-token"]').setAttribute('content', res.csrf);
                    }

                    if (res.status === 'success') {
                        // Bersihkan session storage karena data sudah terhapus
                        sessionStorage.removeItem('selectedSiswaIds');
                        Swal.fire('Berhasil!', res.message, 'success').then(() => window.location.reload());
                    } else {
                        Swal.fire('Gagal!', res.message || 'Terjadi kesalahan.', 'error');
                    }
                } catch (error) {
                    Swal.fire('Gagal!', 'Kesalahan koneksi jaringan.', 'error');
                }
            }
        });
    }


    // ==========================================
    // MANAJEMEN MODAL DAN FUNGSI LAINNYA
    // ==========================================
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

    // ==========================================
    // IMPORT EXCEL
    // ==========================================
    const formImportSiswa = document.getElementById('formImportSiswa');
    if (formImportSiswa) {
        formImportSiswa.addEventListener('submit', async function(e) {
            e.preventDefault();

            const fileInput = document.querySelector('input[name="file_excel"]');
            if (!fileInput.files.length) return;

            tutupModalImport();

            Swal.fire({
                title: 'Sedang Memproses',
                html: `
                    <div class="mt-4 text-left font-sans">
                        <div class="mb-2 text-sm font-bold text-slate-700 flex justify-between">
                            <span id="importStatusText" class="flex items-center text-blue-600">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sinkronisasi Data...
                            </span>
                            <span id="importProgressPercent" class="text-blue-600">0%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-4 mb-2 overflow-hidden border border-slate-300 shadow-inner relative">
                            <div id="importProgressBar" class="bg-blue-600 h-4 rounded-full transition-all duration-500 ease-out relative progress-striped animate-stripes shadow-[0_0_10px_rgba(37,99,235,0.5)]" style="width: 0%"></div>
                        </div>
                        <div class="text-xs text-slate-500 font-medium text-right mb-5 border-b border-slate-200 pb-3">
                            <span id="importProgressCount">0 / 0</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm text-center">
                            <div class="bg-emerald-50 text-emerald-700 py-2 rounded-lg border border-emerald-200 font-bold shadow-sm">
                                ✅ Sukses: <span id="importSuccessCount" class="text-lg">0</span>
                            </div>
                            <div class="bg-amber-50 text-amber-700 py-2 rounded-lg border border-amber-200 font-bold shadow-sm flex flex-col justify-center">
                                <span>⚠️ Gagal</span>
                                <span id="importFailCount" class="text-lg">0</span>
                            </div>
                        </div>
                    </div>
                `,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: async () => {
                    const updateCsrf = (hash) => {
                        if (hash) document.querySelector('meta[name="csrf-token"]').setAttribute('content', hash);
                    };

                    let formData = new window.FormData();
                    formData.append('file_excel', fileInput.files[0]);
                    formData.append('step', 'init');

                    try {
                        let resInit = await fetch('/panel/siswa/import', {
                            method: 'POST',
                            body: formData
                        }).then(res => res.json());
                        updateCsrf(resInit.csrf);

                        if (resInit.status === 'error') {
                            Swal.fire('Gagal!', resInit.message, 'error');
                            return;
                        }

                        const totalRows = resInit.total;
                        const tempId = resInit.temp_id;
                        const chunkSize = 1;

                        let totalSuccess = 0;
                        let totalFailed = 0;

                        document.getElementById('importProgressCount').innerText = `0 / ${totalRows}`;

                        for (let offset = 0; offset < totalRows; offset += chunkSize) {
                            let chunkData = new window.FormData();
                            chunkData.append('step', 'process');
                            chunkData.append('temp_id', tempId);
                            chunkData.append('offset', offset);
                            chunkData.append('limit', chunkSize);

                            let resProcess = await fetch('/panel/siswa/import', {
                                method: 'POST',
                                body: chunkData
                            }).then(res => res.json());
                            updateCsrf(resProcess.csrf);

                            if (resProcess.status === 'error') {
                                Swal.fire('Error Chunking!', resProcess.message, 'error');
                                return;
                            }

                            totalSuccess += resProcess.sukses;
                            totalFailed += resProcess.gagal;

                            let currentProcessed = Math.min(offset + chunkSize, totalRows);
                            let percent = Math.round((currentProcessed / totalRows) * 100);

                            document.getElementById('importProgressBar').style.width = percent + '%';
                            document.getElementById('importProgressPercent').innerText = percent + '%';
                            document.getElementById('importProgressCount').innerText = `${currentProcessed} / ${totalRows}`;
                            document.getElementById('importSuccessCount').innerText = totalSuccess;
                            document.getElementById('importFailCount').innerText = totalFailed;
                        }

                        document.getElementById('importStatusText').innerHTML = `
                            <span class="text-emerald-600 flex items-center font-bold">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Membersihkan data temporari...
                            </span>
                        `;

                        let finishData = new window.FormData();
                        finishData.append('step', 'finish');
                        finishData.append('temp_id', tempId);

                        let resFinish = await fetch('/panel/siswa/import', {
                            method: 'POST',
                            body: finishData
                        }).then(res => res.json());
                        updateCsrf(resFinish.csrf);

                        let finishHTML = '';
                        let iconAlert = 'success';

                        if (totalFailed > 0) {
                            finishHTML = `<div class="text-sm text-left">Proses import selesai dengan beberapa kendala.<br><br><b>✅ Sukses:</b> ${totalSuccess} data berhasil.<br><b>⚠️ Gagal / Duplikat:</b> ${totalFailed} data ditolak.</div>`;
                            iconAlert = 'warning';
                        } else {
                            finishHTML = `<div class="text-sm text-left"><b>🎉 100% Sukses!</b><br><br>Sebanyak <b>${totalSuccess}</b> data berhasil diimport ke sistem tanpa ada yang gagal.</div>`;
                        }

                        Swal.fire({
                            title: 'Selesai!',
                            html: finishHTML,
                            icon: iconAlert,
                            confirmButtonText: 'Tutup & Muat Ulang Halaman'
                        }).then(() => {
                            window.location.reload();
                        });

                    } catch (err) {
                        Swal.fire('Terjadi Kesalahan', 'Gagal memproses jaringan. Periksa log console Anda.', 'error');
                        console.error(err);
                    }
                }
            });
        });
    }
</script>
<?= $this->endSection() ?>