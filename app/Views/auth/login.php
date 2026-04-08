<?php

/**
 * ============================================================================
 * CBT PRO - ENTERPRISE EDITION
 * ============================================================================
 *
 * @package    Nurindra CBT PRO
 * @author     Nurindra
 * @copyright  2026 Nurindra CBT PRO
 * @version    1.0.0
 *
 * @description CBT PRO adalah platform Ujian Berbasis Komputer (Computer Based
 * Test) berskala Enterprise yang dirancang untuk performa tinggi, keamanan
 * absolut, dan manajemen akademik terintegrasi untuk institusi modern.
 * Aplikasi ini boleh digunakan dan di sebarluaskan secara gratis
 *
 * ----------------------------------------------------------------------------
 * HUBUNGI PENGEMBANG:
 * Contact Person : Nurindra
 * Email          : nurindra.id@gmail.com
 * WhatsApp       : +62 812-2032-9780
 * YouTube        : https://www.youtube.com/@nurindraid
 * Instagram      : https://www.instagram.com/kevinecraft
 * TikTok         : https://www.tiktok.com/@kevinecraft1
 * ----------------------------------------------------------------------------
 * PERINGATAN HAK CIPTA:
 * Kode sumber ini dilindungi oleh kekayaan intelektual. Dilarang keras
 * memodifikasi atau menjual ulang bagian manapun dari aplikasi ini 
 * tanpa izin tertulis dari pihak pengembang.
 * ============================================================================
 */


$db = \Config\Database::connect();
$pengaturan = $db->table('pengaturan')->where('id', 1)->get()->getRowArray();
$logo = $pengaturan['logo'] ?? null;
$namaSekolah = $pengaturan['nama_sekolah'] ?? 'Nurindra CBT PRO';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= esc($namaSekolah) ?></title>

    <?php if ($logo): ?>
        <link rel="icon" type="image/png" href="<?= base_url('uploads/' . $logo) ?>">
    <?php endif; ?>

    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>

<body class="bg-slate-100 flex items-center justify-center min-h-screen font-sans text-slate-800 p-4">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-slate-200">
        <div class="p-8 text-center bg-slate-50 border-b border-slate-200">
            <?php if ($logo): ?>
                <img src="<?= base_url('uploads/' . $logo) ?>" alt="Logo Sekolah" class="w-24 h-24 mx-auto object-contain drop-shadow-md mb-4">
            <?php else: ?>
                <div class="w-20 h-20 bg-blue-600 text-white rounded-full flex items-center justify-center text-4xl mx-auto mb-4 shadow-lg">🏫</div>
            <?php endif; ?>

            <h1 class="text-2xl font-bold text-slate-800 uppercase tracking-wide leading-tight"><?= esc($namaSekolah) ?></h1>
            <p class="text-xs text-slate-500 mt-1.5 font-semibold tracking-widest uppercase">Portal Ujian Terpadu</p>
        </div>

        <div class="p-8">
            <form action="/auth/process" method="POST" class="space-y-5">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Username / NISN</label>
                    <input type="text" name="username" required autofocus class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition font-bold text-slate-700 uppercase" autocomplete="off">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="loginPassword" name="password" required class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition pr-12">
                        <button type="button" onclick="togglePassword('loginPassword', 'eyeIconLogin')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-600 transition outline-none">
                            <span id="eyeIconLogin" class="text-xl opacity-70">👁️</span>
                        </button>
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3.5 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2">
                    Masuk Sistem 🚀
                </button>
            </form>
        </div>
    </div>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
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

        function showToast(msg, type = 'success') {
            Toastify({
                text: msg,
                duration: 3500,
                gravity: "top",
                position: "center",
                style: {
                    background: type === 'success' ? "#10b981" : "#ef4444",
                    borderRadius: "10px",
                    fontWeight: "bold"
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