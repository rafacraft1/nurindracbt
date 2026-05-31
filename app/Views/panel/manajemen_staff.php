<?php

/**
 * @var array $staff
 * @var int $totalAdmin
 * @var int $totalPanitia
 */
?>
<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-200 pb-5">
    <div>
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Manajemen Staff & Hak Akses</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola data Guru, delegasi Panitia, dan hak akses Super Admin.</p>
    </div>

    <button onclick="bukaModalStaff()" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-slate-900/20 flex items-center justify-center w-full md:w-auto transform hover:-translate-y-0.5">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Staff
    </button>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
    <div class="bg-gradient-to-br from-indigo-50 to-white border border-indigo-100 p-5 rounded-2xl flex justify-between items-center shadow-sm">
        <div>
            <p class="text-xs font-bold text-indigo-500 uppercase tracking-wider mb-1">Kuota Super Admin</p>
            <p class="text-2xl font-black text-indigo-900"><?= $totalAdmin ?> <span class="text-base font-medium text-indigo-400">/ 1 Orang</span></p>
        </div>
        <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
        </div>
    </div>

    <div class="bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 p-5 rounded-2xl flex justify-between items-center shadow-sm">
        <div>
            <p class="text-xs font-bold text-emerald-500 uppercase tracking-wider mb-1">Kuota Panitia Ujian</p>
            <p class="text-2xl font-black text-emerald-900"><?= $totalPanitia ?> <span class="text-base font-medium text-emerald-400">/ 3 Orang</span></p>
        </div>
        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
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
                    <tr class="hover:bg-blue-50/30 transition-colors h-16">
                        <td class="px-6 py-4 text-center font-medium"><?= $no++ ?></td>

                        <td class="px-6 py-4 font-bold text-slate-800 uppercase">
                            <?= esc($st['nama_lengkap']) ?>
                            <?php if ($st['id'] == session()->get('id')): ?>
                                <span class="ml-2 text-[9px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded border border-blue-200 font-black">ANDA</span>
                            <?php endif; ?>
                        </td>

                        <td class="px-6 py-4 font-mono text-slate-500 font-bold"><?= esc($st['username']) ?></td>

                        <td class="px-6 py-4">
                            <?php if ($st['role'] == 'admin'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-slate-800 text-white shadow-sm border border-slate-700">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    Super Admin
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 mr-2 shadow-sm">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    Guru
                                </span>
                                <?php if ($st['is_panitia'] == 1): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Panitia
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="editStaff(<?= htmlspecialchars(json_encode($st), ENT_QUOTES, 'UTF-8') ?>)" class="p-2 bg-slate-50 hover:bg-blue-50 text-slate-500 hover:text-blue-600 rounded-lg transition-colors border border-slate-200 hover:border-blue-300" title="Edit Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>

                                <?php if ($st['id'] != session()->get('id') && $st['username'] !== 'admin'): ?>
                                    <form action="/panel/manajemen-staff/delete/<?= $st['id'] ?>" method="POST" id="formDelete<?= $st['id'] ?>" class="inline-block">
                                        <?= csrf_field() ?>
                                        <button type="button" onclick="konfirmasiHapus(<?= $st['id'] ?>)" class="p-2 bg-slate-50 hover:bg-red-50 text-slate-500 hover:text-red-600 rounded-lg transition-colors border border-slate-200 hover:border-red-300" title="Hapus Staff">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                <?php elseif ($st['username'] === 'admin' && $st['id'] != session()->get('id')): ?>
                                    <span class="p-2 bg-slate-100 text-slate-400 rounded-lg border border-slate-200 cursor-not-allowed flex items-center justify-center" title="Admin Permanen (Lock)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($staff)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500 bg-slate-50">Belum ada data staff.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalStaff" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform" id="modalStaffContent">
        <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white flex items-center gap-2" id="modalTitle">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Tambah Staff Baru
            </h3>
            <button type="button" onclick="tutupModalStaff()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="/panel/manajemen-staff/store" method="POST" id="formStaff">
            <?= csrf_field() ?>
            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap & Gelar</label>
                    <input type="text" name="nama_lengkap" id="inputNama" required class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none uppercase font-bold text-slate-800 transition shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username Login</label>
                    <input type="text" name="username" id="inputUsername" required class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm text-slate-700 transition shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="inputPassword" placeholder="Kosongkan = password123" class="w-full pl-4 pr-12 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm transition shadow-sm">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-blue-600 transition outline-none">
                            <span id="iconEye" class="p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                    <p class="text-[11px] text-amber-600 mt-1.5 font-medium hidden items-center" id="pwdHelp">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Isi hanya jika ingin mereset password saat ini.
                    </p>
                </div>

                <div class="bg-slate-50 p-4 border border-slate-200 rounded-xl space-y-4" id="boxRole">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Role Utama</label>
                        <select name="role" id="inputRole" onchange="togglePanitia()" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white font-bold text-slate-700 shadow-sm transition">
                            <option value="guru">Guru / Pengawas</option>
                            <option value="admin" id="optAdmin">Super Admin</option>
                        </select>
                    </div>

                    <div id="areaPanitia" class="flex items-center p-3 bg-white border border-slate-200 rounded-xl shadow-sm transition-all">
                        <input type="checkbox" name="is_panitia" id="inputPanitia" value="1" class="w-5 h-5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                        <label for="inputPanitia" class="ml-3 block text-sm font-bold text-slate-700 cursor-pointer select-none w-full" id="labelPanitia">
                            Delegasikan sebagai Panitia
                        </label>
                    </div>
                </div>

            </div>

            <div class="px-6 py-4 bg-slate-50 border-t flex justify-end gap-3 shrink-0">
                <button type="button" onclick="tutupModalStaff()" class="px-5 py-2.5 border border-slate-300 rounded-xl text-slate-700 font-semibold hover:bg-slate-100 transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold shadow-md shadow-blue-600/30 transition flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Simpan Data
                </button>
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

    const totalAdminCount = <?= $totalAdmin ?>;

    const svgEye = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>`;
    const svgEyeOff = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>`;

    function togglePassword() {
        const input = document.getElementById('inputPassword');
        const icon = document.getElementById('iconEye');

        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = svgEyeOff;
        } else {
            input.type = 'password';
            icon.innerHTML = svgEye;
        }
    }

    function togglePanitia() {
        const role = document.getElementById('inputRole').value;
        const areaPanitia = document.getElementById('areaPanitia');
        const chkPanitia = document.getElementById('inputPanitia');

        if (role === 'admin') {
            areaPanitia.classList.add('opacity-50', 'pointer-events-none');
            chkPanitia.checked = false;
        } else {
            areaPanitia.classList.remove('opacity-50', 'pointer-events-none');
        }
    }

    function bukaModalStaff() {
        document.getElementById('modalTitle').innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> Tambah Staff Baru';
        fStaff.action = '/panel/manajemen-staff/store';
        fStaff.reset();

        document.getElementById('inputPassword').type = 'password';
        document.getElementById('iconEye').innerHTML = svgEye;

        document.getElementById('inputUsername').readOnly = false;
        document.getElementById('inputUsername').classList.remove('bg-slate-100', 'text-slate-500', 'cursor-not-allowed');

        document.getElementById('inputRole').disabled = false;
        document.getElementById('inputRole').classList.remove('bg-slate-100', 'text-slate-500', 'cursor-not-allowed');

        document.getElementById('inputPanitia').disabled = false;
        document.getElementById('areaPanitia').classList.remove('opacity-50', 'bg-slate-100');

        let optAdmin = document.getElementById('optAdmin');
        if (totalAdminCount >= 1) {
            optAdmin.hidden = true;
            optAdmin.style.display = 'none';
            optAdmin.disabled = true;
            document.getElementById('inputRole').value = 'guru';
        } else {
            optAdmin.hidden = false;
            optAdmin.style.display = '';
            optAdmin.disabled = false;
            optAdmin.innerText = 'Super Admin';
        }

        let hiddenRole = document.getElementById('hiddenRole');
        if (hiddenRole) hiddenRole.remove();

        // FIX CSS CONFLICT TAILWIND 
        document.getElementById('pwdHelp').classList.add('hidden');
        document.getElementById('pwdHelp').classList.remove('flex');
        togglePanitia();

        toggleModal(mStaff, cStaff, true);
    }

    function editStaff(data) {
        document.getElementById('modalTitle').innerHTML = '<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> Edit Data Staff';
        fStaff.action = '/panel/manajemen-staff/update/' + data.id;

        document.getElementById('inputNama').value = data.nama_lengkap;
        document.getElementById('inputUsername').value = data.username;
        document.getElementById('inputPassword').value = '';

        document.getElementById('inputPassword').type = 'password';
        document.getElementById('iconEye').innerHTML = svgEye;

        let optAdmin = document.getElementById('optAdmin');
        if (data.role === 'admin') {
            optAdmin.hidden = false;
            optAdmin.style.display = '';
            optAdmin.disabled = false;
            optAdmin.innerText = 'Super Admin';
        } else if (totalAdminCount >= 1) {
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
            document.getElementById('areaPanitia').classList.add('opacity-50', 'bg-slate-100');

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
            document.getElementById('areaPanitia').classList.remove('opacity-50', 'bg-slate-100');

            let hiddenRole = document.getElementById('hiddenRole');
            if (hiddenRole) hiddenRole.remove();
        }

        // FIX CSS CONFLICT TAILWIND 
        document.getElementById('pwdHelp').classList.remove('hidden');
        document.getElementById('pwdHelp').classList.add('flex');
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
            text: "Data staf akan dihapus permanen. Aksi ini tidak dapat dibatalkan.",
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