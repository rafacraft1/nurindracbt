<?php
$dbPanel = \Config\Database::connect();
$pengaturanPanel = $dbPanel->table('pengaturan')->where('id', 1)->get()->getRowArray();
$logoSekolah = $pengaturanPanel['logo'] ?? null;
$namaSekolah = $pengaturanPanel['nama_sekolah'] ?? 'CBT PRO';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title>Panel - <?= esc($namaSekolah) ?></title>

    <?php if ($logoSekolah): ?>
        <link rel="icon" type="image/png" href="<?= base_url('uploads/' . $logoSekolah) ?>">
    <?php endif; ?>

    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-slate-50 flex h-screen overflow-hidden font-sans text-gray-800">

    <?php
    $role       = session()->get('role');
    $isPanitia  = session()->get('is_panitia');
    $currentUrl = uri_string();

    $isActive = function ($path) use ($currentUrl) {
        if (strpos($currentUrl, $path) !== false) {
            return 'bg-blue-600 text-white shadow-md active-menu';
        }
        return 'text-slate-300 hover:bg-slate-700 hover:text-white';
    };
    ?>

    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden transition-opacity md:hidden"></div>

    <aside id="sidebar" class="bg-slate-800 text-slate-300 w-64 shrink-0 shadow-xl flex flex-col transition-all duration-300 z-50 fixed inset-y-0 left-0 md:static transform -translate-x-full md:translate-x-0 h-full">
        <div class="h-16 flex items-center justify-center border-b border-slate-700 bg-slate-900 shrink-0">
            <h1 class="font-bold text-2xl tracking-wider text-blue-400">CBT<span class="text-white">PRO</span></h1>
        </div>

        <nav id="sidebarNav" class="flex-1 p-4 space-y-1.5 overflow-y-auto no-scrollbar scroll-smooth">
            <a href="/panel/dashboard" class="<?= $isActive('panel/dashboard') ?> flex items-center px-4 py-2.5 rounded-lg transition-all font-medium text-sm">
                <span class="mr-3 text-lg">📊</span> Dashboard
            </a>

            <?php if ($role === 'admin'): ?>
                <div class="pt-4 pb-1">
                    <p class="px-4 text-[10px] font-bold tracking-wider text-slate-500 uppercase">Super Admin</p>
                </div>
                <a href="/panel/manajemen-staff" class="<?= $isActive('panel/manajemen-staff') ?> flex items-center px-4 py-2.5 rounded-lg transition-all font-medium text-sm">
                    <span class="mr-3 text-lg">🛡️</span> Manajemen Staff
                </a>
                <a href="/panel/pengaturan" class="<?= $isActive('panel/pengaturan') ?> flex items-center px-4 py-2.5 rounded-lg transition-all font-medium text-sm">
                    <span class="mr-3 text-lg">⚙️</span> Pengaturan Sistem
                </a>
            <?php endif; ?>

            <?php if ($role === 'admin' || $isPanitia == 1): ?>
                <div class="pt-4 pb-1">
                    <p class="px-4 text-[10px] font-bold tracking-wider text-slate-500 uppercase">Kepanitiaan</p>
                </div>
                <a href="/panel/ruangan" class="<?= $isActive('panel/ruangan') ?> flex items-center px-4 py-2.5 rounded-lg transition-all font-medium text-sm">
                    <span class="mr-3 text-lg">🚪</span> Ruangan Ujian
                </a>
                <a href="/panel/siswa" class="<?= $isActive('panel/siswa') ?> flex items-center px-4 py-2.5 rounded-lg transition-all font-medium text-sm">
                    <span class="mr-3 text-lg">👥</span> Data Siswa
                </a>
                <a href="/panel/jenis-ujian" class="<?= $isActive('panel/jenis-ujian') ?> flex items-center px-4 py-2.5 rounded-lg transition-all font-medium text-sm">
                    <span class="mr-3 text-lg">🏷️</span> Jenis Ujian
                </a>
                <a href="/panel/mapel" class="<?= $isActive('panel/mapel') ?> flex items-center px-4 py-2.5 rounded-lg transition-all font-medium text-sm">
                    <span class="mr-3 text-lg">📚</span> Mata Pelajaran
                </a>
                <a href="/panel/jadwal" class="<?= $isActive('panel/jadwal') ?> flex items-center px-4 py-2.5 rounded-lg transition-all font-medium text-sm">
                    <span class="mr-3 text-lg">📅</span> Jadwal & Pengawas
                </a>
                <a href="/panel/cetak-kartu" class="<?= $isActive('panel/cetak-kartu') ?> flex items-center px-4 py-2.5 rounded-lg transition-all font-medium text-sm">
                    <span class="mr-3 text-lg">🖨️</span> Cetak Kartu
                </a>
            <?php endif; ?>

            <?php if ($role === 'admin' || $role === 'guru'): ?>
                <div class="pt-4 pb-1">
                    <p class="px-4 text-[10px] font-bold tracking-wider text-slate-500 uppercase">Akademik & Ujian</p>
                </div>
                <a href="/panel/bank-soal" class="<?= $isActive('panel/bank-soal') ?> flex items-center px-4 py-2.5 rounded-lg transition-all font-medium text-sm">
                    <span class="mr-3 text-lg">📝</span> Bank Soal
                </a>
                <a href="/panel/penilaian" class="<?= $isActive('panel/penilaian') ?> flex items-center px-4 py-2.5 rounded-lg transition-all font-medium text-sm">
                    <span class="mr-3 text-lg">📝</span> Penilaian & Koreksi
                </a>
                <a href="/panel/ruang-pengawas" class="<?= $isActive('panel/ruang-pengawas') ?> flex items-center px-4 py-2.5 rounded-lg transition-all font-medium text-sm">
                    <span class="mr-3 text-lg">👁️</span> Ruang Pengawas
                </a>
            <?php endif; ?>
        </nav>

        <div class="p-4 border-t border-slate-700 bg-slate-900 shrink-0">
            <button onclick="confirmLogout()" class="w-full bg-slate-700 hover:bg-red-500 text-white py-2 rounded-lg text-sm transition-colors font-semibold shadow">
                Keluar Sistem
            </button>
        </div>
    </aside>

    <script>
        if (localStorage.getItem('sidebar_collapsed') === 'true' && window.innerWidth >= 768) {
            document.getElementById('sidebar').classList.add('-ml-64');
        }
    </script>

    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-slate-100 relative">

        <header class="h-16 bg-white border-b border-slate-200 px-4 flex justify-between items-center shadow-sm shrink-0 z-20">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 bg-slate-100 hover:bg-blue-100 hover:text-blue-600 text-slate-600 rounded-lg transition" title="Show/Hide Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h2 class="font-bold text-lg text-slate-700 hidden sm:block truncate"><?= $title ?? 'Dashboard' ?></h2>
            </div>

            <div class="hidden md:flex items-center gap-2 bg-slate-800 text-slate-100 px-3 py-1.5 rounded-lg ml-auto mr-4 shadow-inner border border-slate-700">
                <span class="text-sm">🕒</span>
                <span id="jamServerPanel" class="font-mono font-bold text-sm tracking-widest text-blue-400">--:--:--</span>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-slate-800 leading-tight"><?= session()->get('nama_lengkap') ?? 'User' ?></p>
                    <p class="text-[10px] font-semibold text-blue-600 uppercase">
                        <?= $role === 'admin' ? 'Super Admin' : ($isPanitia ? 'Guru & Panitia' : 'Guru') ?>
                    </p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 border border-blue-200 flex items-center justify-center font-bold shadow-inner shrink-0">
                    <?= substr(session()->get('nama_lengkap') ?? 'A', 0, 1) ?>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 md:p-6 lg:p-8 relative">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (window.innerWidth >= 768) {
                sidebar.classList.toggle('-ml-64');
                localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('-ml-64'));
            } else {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const activeMenu = document.querySelector('.active-menu');
            const sidebarNav = document.getElementById('sidebarNav');

            if (activeMenu && sidebarNav) {
                const offset = activeMenu.offsetTop - (sidebarNav.clientHeight / 2) + (activeMenu.clientHeight / 2);
                setTimeout(() => {
                    sidebarNav.scrollTo({
                        top: Math.max(0, offset),
                        behavior: 'smooth'
                    });
                }, 100);
            }
        });

        (function() {
            const originalFetch = window.fetch;
            window.fetch = async function() {
                let [resource, config] = arguments;
                if (config === undefined) config = {};
                if (config.method && ['POST', 'PUT', 'DELETE', 'PATCH'].includes(config.method.toUpperCase())) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    config.headers = {
                        ...config.headers,
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    };
                }
                return await originalFetch(resource, config);
            };
        })();

        function confirmLogout() {
            Swal.fire({
                title: 'Keluar sistem?',
                text: "Sesi kerja Anda akan diakhiri.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Keluar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/logout';
                }
            })
        }

        function showToast(msg, type = 'success') {
            Toastify({
                text: msg,
                duration: 3500,
                gravity: "top",
                position: "right",
                style: {
                    background: type === 'success' ? "#10b981" : "#ef4444",
                    borderRadius: "8px",
                    fontWeight: "bold",
                    fontSize: "14px"
                }
            }).showToast();
        }

        <?php if (session()->getFlashdata('error')) : ?>
            showToast("<?= esc(session()->getFlashdata('error'), 'js') ?>", 'error');
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')) : ?>
            showToast("<?= esc(session()->getFlashdata('success'), 'js') ?>", 'success');
        <?php endif; ?>

        // ==========================================
        // ENGINE JAM SERVER PANEL
        // ==========================================
        let panelServerTime = new window.Date("<?= date('Y-m-d\TH:i:s') ?>");
        setInterval(() => {
            panelServerTime.setSeconds(panelServerTime.getSeconds() + 1);
            let h = String(panelServerTime.getHours()).padStart(2, '0');
            let m = String(panelServerTime.getMinutes()).padStart(2, '0');
            let s = String(panelServerTime.getSeconds()).padStart(2, '0');
            let elJam = document.getElementById('jamServerPanel');
            if (elJam) elJam.innerText = `${h}:${m}:${s} WIB`;
        }, 1000);
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>