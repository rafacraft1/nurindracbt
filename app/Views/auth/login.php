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
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>

<body class="bg-white min-h-screen font-sans text-slate-800 flex overflow-hidden">

    <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-blue-800 via-blue-600 to-indigo-900 items-center justify-center overflow-hidden">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-400/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30rem] h-[30rem] bg-indigo-400/20 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNykiLz48L3N2Zz4=')] opacity-50"></div>

        <div class="relative z-10 text-center px-12 max-w-lg flex flex-col items-center">
            <div class="bg-white/10 p-6 rounded-3xl backdrop-blur-sm border border-white/20 shadow-2xl mb-8">
                <img src="<?= $urlLogo ?>" alt="Logo Sekolah" class="w-32 h-32 object-contain drop-shadow-xl">
            </div>
            <h1 class="text-4xl font-black text-white tracking-wider mb-4 drop-shadow-lg"><?= esc($namaSekolah) ?></h1>
            <p class="text-blue-100 text-lg font-medium leading-relaxed">
                Platform Ujian Berbasis Komputer. Modern, Aman, dan Terintegrasi untuk evaluasi akademik yang presisi.
            </p>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 relative bg-slate-50 lg:bg-white">

        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl lg:hidden"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl lg:hidden"></div>

        <div class="max-w-md w-full space-y-8 relative z-10">

            <div class="text-center lg:text-left">
                <img src="<?= $urlLogo ?>" alt="Logo" class="w-20 h-20 mx-auto lg:hidden object-contain mb-6 drop-shadow-sm">

                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Selamat Datang <span class="animate-wave inline-block origin-bottom-right">👋</span></h2>
                <p class="text-slate-500 mt-2 font-medium">Silakan masuk menggunakan identitas resmi Anda.</p>
            </div>

            <form action="/auth/process" method="POST" class="space-y-6 mt-8">
                <?= csrf_field() ?>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Username / NISN</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" name="username" required autofocus
                            class="w-full pl-11 pr-4 py-3.5 bg-white lg:bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-slate-800 uppercase placeholder:text-slate-400 placeholder:font-medium placeholder:normal-case shadow-sm"
                            placeholder="Masukkan ID Login" autocomplete="off">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password" id="loginPassword" name="password" required
                            class="w-full pl-11 pr-12 py-3.5 bg-white lg:bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all shadow-sm font-medium text-slate-800"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword('loginPassword', 'eyeIconContainer')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-600 transition-colors outline-none">
                            <div id="eyeIconContainer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-slate-800 text-white font-bold py-4 rounded-xl hover:bg-blue-600 transition-all duration-300 shadow-lg shadow-slate-800/20 hover:shadow-blue-600/30 flex items-center justify-center group transform hover:-translate-y-0.5">
                        <span class="tracking-wide">Masuk Sistem</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <div class="text-center mt-8 pt-8 border-t border-slate-200/60">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">CBT PRO Enterprise &copy; <?= date('Y') ?></p>
            </div>

        </div>
    </div>

    <style>
        @keyframes wave {

            0%,
            100% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(15deg);
            }

            75% {
                transform: rotate(-10deg);
            }
        }

        .animate-wave {
            animation: wave 1.5s infinite;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
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
                position: "right",
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