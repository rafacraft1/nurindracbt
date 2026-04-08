<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-200 pb-5">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Manajemen Staff & Hak Akses</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola data Guru, delegasi Panitia, dan hak akses Super Admin.</p>
    </div>

    <button onclick="bukaModalStaff()" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow-lg flex items-center justify-center w-full md:w-auto">
        <span class="mr-2">➕</span> Tambah Staff Baru
    </button>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-indigo-50 border border-indigo-200 p-4 rounded-xl flex justify-between items-center">
        <div>
            <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider">Kuota Super Admin</p>
            <p class="text-xl font-black text-indigo-800"><?= $totalAdmin ?> <span class="text-sm font-medium text-indigo-600">/ 1 Orang</span></p>
        </div>
        <div class="text-3xl">🛡️</div>
    </div>
    <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl flex justify-between items-center">
        <div>
            <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider">Kuota Panitia Ujian</p>
            <p class="text-xl font-black text-emerald-800"><?= $totalPanitia ?> <span class="text-sm font-medium text-emerald-600">/ 3 Orang</span></p>
        </div>
        <div class="text-3xl">📋</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-100 text-slate-800 font-semibold uppercase text-[11px] tracking-wider">
                <tr>
                    <th class="px-6 py-4 w-16 text-center">No</th>
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">Username Login</th>
                    <th class="px-6 py-4">Hak Akses / Role</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $no = 1;
                foreach ($staff as $st): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-center font-medium"><?= $no++ ?></td>

                        <td class="px-6 py-4 font-bold text-slate-800 uppercase">
                            <?= esc($st['nama_lengkap']) ?>
                            <?php if ($st['id'] == session()->get('id')): ?>
                                <span class="ml-2 text-[9px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full border border-blue-200">Anda</span>
                            <?php endif; ?>
                        </td>

                        <td class="px-6 py-4 font-mono text-blue-600 font-bold"><?= esc($st['username']) ?></td>

                        <td class="px-6 py-4">
                            <?php if ($st['role'] == 'admin'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-slate-800 text-white shadow-sm">
                                    🛡️ Super Admin
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200 mr-1">
                                    👨‍🏫 Guru / Pengawas
                                </span>
                                <?php if ($st['is_panitia'] == 1): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        + 📋 Panitia
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick='editStaff(<?= json_encode($st) ?>)' class="p-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded transition" title="Edit Data">
                                    ✏️
                                </button>

                                <?php if ($st['id'] != session()->get('id') && $st['username'] !== 'admin'): ?>
                                    <form action="/panel/manajemen-staff/delete/<?= $st['id'] ?>" method="POST" id="formDelete<?= $st['id'] ?>" class="inline-block">
                                        <?= csrf_field() ?>
                                        <button type="button" onclick="konfirmasiHapus(<?= $st['id'] ?>)" class="p-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded transition border border-red-200" title="Hapus Staff">
                                            🗑️
                                        </button>
                                    </form>
                                <?php elseif ($st['username'] === 'admin' && $st['id'] != session()->get('id')): ?>
                                    <span class="p-1.5 bg-slate-100 text-slate-400 rounded border border-slate-200 cursor-not-allowed text-[11px] font-bold" title="Admin Permanen">🔒</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalStaff" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform" id="modalStaffContent">
        <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white" id="modalTitle">Tambah Staff Baru</h3>
            <button type="button" onclick="tutupModalStaff()" class="text-slate-400 hover:text-white">✖</button>
        </div>

        <form action="/panel/manajemen-staff/store" method="POST" id="formStaff">
            <?= csrf_field() ?>
            <div class="p-6 space-y-4">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap & Gelar</label>
                    <input type="text" name="nama_lengkap" id="inputNama" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none uppercase">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Username Login</label>
                    <input type="text" name="username" id="inputUsername" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="inputPassword" placeholder="Kosongkan untuk default: password123" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm pr-10">
                        <button type="button" onclick="togglePassword('inputPassword', 'eyeIconStaff')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-blue-600 transition outline-none">
                            <span id="eyeIconStaff" class="text-lg opacity-70">👁️</span>
                        </button>
                    </div>
                    <p class="text-[10px] text-amber-600 mt-1 hidden" id="pwdHelp">Isi hanya jika ingin mengubah password saat ini.</p>
                </div>

                <div class="bg-slate-50 p-4 border border-slate-200 rounded-lg space-y-4" id="boxRole">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Role Utama</label>
                        <select name="role" id="inputRole" onchange="togglePanitia()" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white font-bold text-slate-700">
                            <option value="guru">Guru Mata Pelajaran</option>
                            <option value="admin" id="optAdmin">Super Admin</option>
                        </select>
                    </div>

                    <div id="areaPanitia" class="flex items-center">
                        <input type="checkbox" name="is_panitia" id="inputPanitia" value="1" class="w-5 h-5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                        <label for="inputPanitia" class="ml-2 block text-sm font-bold text-emerald-700 cursor-pointer" id="labelPanitia">
                            Delegasikan sebagai Panitia Ujian
                        </label>
                    </div>
                </div>

            </div>

            <div class="px-6 py-4 bg-slate-50 border-t flex justify-end gap-3">
                <button type="button" onclick="tutupModalStaff()" class="px-4 py-2 border rounded-lg text-slate-700 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900 font-bold shadow">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const mStaff = document.getElementById('modalStaff');
    const cStaff = document.getElementById('modalStaffContent');
    const fStaff = document.getElementById('formStaff');

    // Variabel pengecekan kuota admin dari PHP
    const totalAdminCount = <?= $totalAdmin ?>;

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.innerText = '🙈';
        } else {
            input.type = 'password';
            icon.innerText = '👁️';
        }
    }

    function togglePanitia() {
        const role = document.getElementById('inputRole').value;
        const areaPanitia = document.getElementById('areaPanitia');
        const chkPanitia = document.getElementById('inputPanitia');

        if (role === 'admin') {
            areaPanitia.classList.add('hidden');
            chkPanitia.checked = false;
        } else {
            areaPanitia.classList.remove('hidden');
        }
    }

    function bukaModalStaff() {
        document.getElementById('modalTitle').innerText = 'Tambah Staff Baru';
        fStaff.action = '/panel/manajemen-staff/store';
        fStaff.reset();

        document.getElementById('inputPassword').type = 'password';
        document.getElementById('eyeIconStaff').innerText = '👁️';

        document.getElementById('inputUsername').readOnly = false;
        document.getElementById('inputUsername').classList.remove('bg-slate-100', 'text-slate-500', 'cursor-not-allowed');

        document.getElementById('inputRole').disabled = false;
        document.getElementById('inputRole').classList.remove('bg-slate-100', 'text-slate-500', 'cursor-not-allowed');

        document.getElementById('inputPanitia').disabled = false;
        document.getElementById('labelPanitia').classList.remove('text-slate-400');

        // SEMBUNYIKAN OPSI ADMIN SEPENUHNYA SAAT TAMBAH BARU (JIKA KUOTA PENUH)
        let optAdmin = document.getElementById('optAdmin');
        if (totalAdminCount >= 1) {
            optAdmin.hidden = true; // Sembunyikan dari HTML
            optAdmin.style.display = 'none'; // Sembunyikan dari CSS Render
            optAdmin.disabled = true; // Cegah terpilih
            document.getElementById('inputRole').value = 'guru'; // Paksa ke guru
        } else {
            optAdmin.hidden = false;
            optAdmin.style.display = '';
            optAdmin.disabled = false;
            optAdmin.innerText = 'Super Admin';
        }

        let hiddenRole = document.getElementById('hiddenRole');
        if (hiddenRole) hiddenRole.remove();

        document.getElementById('pwdHelp').classList.add('hidden');
        togglePanitia();

        toggleModal(mStaff, cStaff, true);
    }

    function editStaff(data) {
        document.getElementById('modalTitle').innerText = 'Edit Data Staff';
        fStaff.action = '/panel/manajemen-staff/update/' + data.id;

        document.getElementById('inputNama').value = data.nama_lengkap;
        document.getElementById('inputUsername').value = data.username;
        document.getElementById('inputPassword').value = '';

        document.getElementById('inputPassword').type = 'password';
        document.getElementById('eyeIconStaff').innerText = '👁️';

        // MUNCULKAN OPSI ADMIN HANYA JIKA SEDANG MENGEDIT AKUN ADMIN
        let optAdmin = document.getElementById('optAdmin');
        if (data.role === 'admin') {
            optAdmin.hidden = false;
            optAdmin.style.display = '';
            optAdmin.disabled = false;
            optAdmin.innerText = 'Super Admin';
        } else if (totalAdminCount >= 1) {
            // Sembunyikan opsi admin jika sedang edit guru dan kuota admin sudah penuh
            optAdmin.hidden = true;
            optAdmin.style.display = 'none';
            optAdmin.disabled = true;
        } else {
            optAdmin.hidden = false;
            optAdmin.style.display = '';
            optAdmin.disabled = false;
            optAdmin.innerText = 'Super Admin';
        }

        document.getElementById('inputRole').value = data.role;
        document.getElementById('inputPanitia').checked = data.is_panitia == 1;

        if (data.username === 'admin') {
            document.getElementById('inputUsername').readOnly = true;
            document.getElementById('inputUsername').classList.add('bg-slate-100', 'text-slate-500', 'cursor-not-allowed');

            document.getElementById('inputRole').disabled = true;
            document.getElementById('inputRole').classList.add('bg-slate-100', 'text-slate-500', 'cursor-not-allowed');

            document.getElementById('inputPanitia').disabled = true;
            document.getElementById('labelPanitia').classList.add('text-slate-400');

            if (!document.getElementById('hiddenRole')) {
                let hiddenRole = document.createElement('input');
                hiddenRole.type = 'hidden';
                hiddenRole.name = 'role';
                hiddenRole.id = 'hiddenRole';
                hiddenRole.value = 'admin';
                fStaff.appendChild(hiddenRole);
            }
        } else {
            document.getElementById('inputUsername').readOnly = false;
            document.getElementById('inputUsername').classList.remove('bg-slate-100', 'text-slate-500', 'cursor-not-allowed');

            document.getElementById('inputRole').disabled = false;
            document.getElementById('inputRole').classList.remove('bg-slate-100', 'text-slate-500', 'cursor-not-allowed');

            document.getElementById('inputPanitia').disabled = false;
            document.getElementById('labelPanitia').classList.remove('text-slate-400');

            let hiddenRole = document.getElementById('hiddenRole');
            if (hiddenRole) hiddenRole.remove();
        }

        document.getElementById('pwdHelp').classList.remove('hidden');
        togglePanitia();

        toggleModal(mStaff, cStaff, true);
    }

    function tutupModalStaff() {
        toggleModal(mStaff, cStaff, false);
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
            title: 'Hapus Staff?',
            text: "Data staf akan dihapus dari sistem secara permanen.",
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