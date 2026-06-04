<?php

/**
 * ============================================================================
 * CBT PRO - ENTERPRISE EDITION
 * ============================================================================
 */

$db = \Config\Database::connect();
$pengaturan = $db->table('pengaturan')->where('id', 1)->get()->getRowArray();
$logo = $pengaturan['logo'] ?? null;
$namaSekolah = $pengaturan['nama_sekolah'] ?? 'Nurindra CBT PRO';

$urlLogo = $logo ? base_url('uploads/' . $logo) : base_url('assets/img/logo.png');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - <?= esc($namaSekolah) ?></title>

    <link rel="icon" type="image/png" href="<?= $urlLogo ?>">
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?= base_url('css/toastify.min.css') ?>">
</head>

<body class="bg-slate-100 flex items-center justify-center min-h-screen font-sans text-slate-800 p-4 relative overflow-hidden">

    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>

    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 w-full max-w-md overflow-hidden border border-slate-200 relative z-10">
        <div class="p-8 text-center bg-slate-50 border-b border-slate-200 relative overflow-hidden">
            <div class="absolute inset-0 bg-blue-600/5 -skew-y-6 transform origin-top-left z-0"></div>

            <img src="<?= $urlLogo ?>" alt="Logo Sekolah" class="w-24 h-24 mx-auto object-contain drop-shadow-md mb-4 relative z-10">

            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-wide leading-tight relative z-10"><?= esc($namaSekolah) ?></h1>
            <p class="text-[11px] text-blue-600 mt-1.5 font-bold tracking-[0.2em] uppercase relative z-10">Portal Ujian Terpadu</p>
        </div>

        <div class="p-8">
            <form action="/auth/process" method="POST" class="space-y-5">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Username / NISN</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" name="username" required autofocus class="w-full pl-11 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-bold text-slate-700 uppercase placeholder:text-slate-400 placeholder:font-medium placeholder:normal-case shadow-inner" placeholder="Masukkan ID Login" autocomplete="off">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password" id="loginPassword" name="password" required class="w-full pl-11 pr-12 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition shadow-inner font-medium text-slate-800" placeholder="••••••••">
                        <button type="button" onclick="togglePassword('loginPassword', 'eyeIconContainer')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-600 transition outline-none">
                            <div id="eyeIconContainer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3.5 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-500/30 flex items-center justify-center mt-2 group transform hover:-translate-y-0.5">
                    Masuk Sistem
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <script src="<?= base_url('js/toastify.min.js') ?>"></script>
    <script>
        // Logika Toggle Password menggunakan SVG murni
        const iconEye = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>`;
        const iconEyeSlash = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a9.953 9.953 0 013.29-1.56m0 0L12 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>`;

        function togglePassword(inputId, containerId) {
            const input = document.getElementById(inputId);
            const container = document.getElementById(containerId);

            if (input.type === 'password') {
                input.type = 'text';
                container.innerHTML = iconEyeSlash;
            } else {
                input.type = 'password';
                container.innerHTML = iconEye;
            }
        }

        function showToast(msg, type = 'success') {
            Toastify({
                text: msg,
                duration: 4000,
                gravity: "top",
                position: "center",
                style: {
                    background: type === 'success' ? "#10b981" : "#ef4444",
                    borderRadius: "10px",
                    fontWeight: "bold",
                    boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)"
                }
            }).showToast();
        }

        <?php if (session()->getFlashdata('error')) : ?>showToast("<?= esc(session()->getFlashdata('error'), 'js') ?>", 'error');
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')) : ?>showToast("<?= esc(session()->getFlashdata('success'), 'js') ?>", 'success');
        <?php endif; ?>
    </script>
</body>

</html>