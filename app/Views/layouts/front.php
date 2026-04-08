<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= $title ?? 'Ujian Siswa' ?></title>

    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <style>
        /* Mencegah zoom tidak sengaja di HP saat double tap */
        body {
            touch-action: manipulation;
        }
    </style>
</head>

<body class="bg-gray-200 min-h-screen flex flex-col font-sans text-gray-800">

    <header class="bg-blue-700 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-white text-blue-700 rounded-full flex items-center justify-center font-bold text-sm">
                    CBT
                </div>
                <div class="leading-tight">
                    <h1 class="font-bold text-sm md:text-base truncate max-w-[150px] md:max-w-xs"><?= session()->get('nama_lengkap') ?? 'Siswa' ?></h1>
                    <p class="text-[10px] md:text-xs text-blue-200"><?= session()->get('nisn') ?? 'NISN' ?></p>
                </div>
            </div>

            <div id="header-action-area">
                <button onclick="window.location.href='/logout'" class="text-xs bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded font-semibold transition">
                    Keluar
                </button>
            </div>
        </div>
    </header>

    <main class="flex-1 w-full max-w-4xl mx-auto p-2 md:p-4 flex flex-col">
        <?= $this->renderSection('content') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
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

        // Global Toast Helper (Bisa dipanggil kapan saja)
        function showToast(msg, type = 'success') {
            Toastify({
                text: msg,
                duration: type === 'warning' ? 5000 : 3000, // Error/Warning tampil lebih lama
                gravity: "bottom", // Di HP lebih enak muncul di bawah
                position: "center",
                style: {
                    background: type === 'success' ? "#10b981" : (type === 'warning' ? "#f59e0b" : "#ef4444"),
                    borderRadius: "8px",
                    fontSize: "14px"
                }
            }).showToast();
        }
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>