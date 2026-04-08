<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= $title ?></title>
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>

<body class="bg-slate-100 font-sans text-slate-800 min-h-screen flex flex-col select-none">

    <header class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center font-bold text-xl shadow-inner">
                    <?= substr(session()->get('nama_lengkap'), 0, 1) ?>
                </div>
                <div class="hidden sm:block">
                    <h1 class="font-bold text-sm text-slate-800 uppercase leading-tight truncate w-48"><?= esc(session()->get('nama_lengkap')) ?></h1>
                    <p class="text-[10px] text-slate-500 font-mono"><?= esc(session()->get('nisn')) ?></p>
                </div>
            </div>

            <div class="text-center flex-1 md:flex-none px-4">
                <h2 class="font-bold text-slate-800 uppercase tracking-wider text-sm md:text-base truncate"><?= esc($jadwal['nama_mapel']) ?></h2>
            </div>

            <div class="flex items-center gap-2 bg-slate-800 px-3 py-1.5 md:px-4 md:py-2 rounded-lg shadow-inner">
                <span class="text-sm md:text-xl">⏱️</span>
                <span id="timerDisplay" class="font-mono text-white font-bold text-sm md:text-lg tracking-widest">
                    --:--:--
                </span>
            </div>
        </div>
    </header>

    <main class="flex-1 w-full max-w-7xl mx-auto p-4 md:p-6 flex flex-col md:flex-row gap-6 items-start pb-24 md:pb-6">

        <div class="w-full md:w-8/12 lg:w-9/12 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col min-h-[60vh]">

            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex justify-between items-center">
                <h3 class="font-bold text-slate-700 text-lg">Soal No. <span id="nomorSoalAktif" class="text-blue-600 text-xl">1</span></h3>
                <span id="badgeJenisSoal" class="px-3 py-1 bg-indigo-100 text-indigo-700 font-bold text-[10px] uppercase tracking-wider rounded-md border border-indigo-200">
                    PILIHAN GANDA
                </span>
            </div>

            <div class="p-6 md:p-8 flex-1 text-slate-700 text-base md:text-lg leading-relaxed font-medium" id="kontenSoal">
                <div class="animate-pulse flex space-x-4">
                    <div class="flex-1 space-y-4 py-1">
                        <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                        <div class="space-y-2">
                            <div class="h-4 bg-slate-200 rounded"></div>
                            <div class="h-4 bg-slate-200 rounded w-5/6"></div>
                        </div>
                    </div>
                </div>
                <p class="text-center text-sm text-slate-400 mt-4">Menyiapkan kanvas ujian...</p>
            </div>

            <div class="px-6 md:px-8 pb-8" id="areaJawaban"></div>

            <div class="bg-slate-50 border-t border-slate-200 p-4 md:p-6 flex justify-between items-center gap-2">
                <button onclick="navigasiSoal('prev')" id="btnPrev" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-600 rounded-lg font-bold shadow-sm hover:bg-slate-100 transition disabled:opacity-50 text-sm md:text-base">
                    ⬅️<span class="hidden sm:inline ml-2">Sebelumnya</span>
                </button>

                <label class="flex items-center gap-2 cursor-pointer bg-amber-50 border border-amber-200 px-4 py-2.5 rounded-lg hover:bg-amber-100 transition">
                    <input type="checkbox" id="checkRagu" onchange="toggleRagu()" class="w-5 h-5 text-amber-500 rounded border-slate-300 focus:ring-amber-500 cursor-pointer">
                    <span class="font-bold text-amber-700 text-sm md:text-base">Ragu-ragu</span>
                </label>

                <button onclick="navigasiSoal('next')" id="btnNext" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg font-bold shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition text-sm md:text-base flex items-center">
                    <span class="hidden sm:inline mr-2" id="textBtnNext">Selanjutnya</span>➡️
                </button>
            </div>
        </div>

        <div class="w-full md:w-4/12 lg:w-3/12 flex flex-col gap-4">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-800 text-white px-5 py-3 border-b border-slate-700">
                    <h3 class="font-bold text-sm tracking-wide">Navigasi Soal</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-4 gap-2" id="gridNomorSoal"></div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col gap-2 text-[11px] font-bold text-slate-500">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-emerald-500 rounded-sm"></div> Sudah Dijawab
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-amber-400 rounded-sm"></div> Ragu-ragu
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-white border border-slate-300 rounded-sm"></div> Belum Dijawab
                        </div>
                    </div>
                </div>
            </div>

            <button onclick="konfirmasiSelesai()" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-lg shadow-emerald-500/30 transition text-sm uppercase tracking-wider flex justify-center items-center gap-2">
                ✅ Selesai Ujian
            </button>
        </div>
    </main>

    <form action="/ujian/submit" method="POST" id="formSubmitUjian" class="hidden">
        <?= csrf_field() ?>
        <input type="hidden" name="payload_jawaban" id="payloadJawaban">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        const JADWAL_ID = <?= $jadwal['id'] ?? 0 ?>;
        const SISWA_ID = <?= session()->get('id') ?? 0 ?>;
        const DURASI = <?= $jadwal['durasi'] ?? 90 ?>;
        const JSON_URL = "<?= base_url('data_soal/jadwal_' . ($jadwal['id'] ?? 0) . '.json') ?>";

        // ENGINE WAKTU ABSOLUT
        const WAKTU_SELESAI_MS = <?= strtotime($jadwal['waktu_selesai']) * 1000 ?>;
        const GRACE_PERIOD_MS = 15 * 60 * 1000; // Toleransi 15 Menit
        const ABSOLUTE_DEADLINE = WAKTU_SELESAI_MS + GRACE_PERIOD_MS;

        // Keamanan Dasar
        document.addEventListener('contextmenu', event => event.preventDefault());
        document.onkeydown = function(e) {
            if (e.keyCode == 123 || (e.ctrlKey && e.shiftKey && (e.keyCode == 73 || e.keyCode == 74)) || (e.ctrlKey && e.keyCode == 85)) {
                return false;
            }
        };

        window.onerror = function(msg) {
            document.getElementById('kontenSoal').innerHTML = `<p class="text-red-600 font-bold text-center">Gagal merender soal. Terjadi kesalahan sistem: ${msg}</p>`;
        };

        // State Management
        let bankSoal = [];
        let indexAktif = 0;
        let jawabanSiswa = {};
        let intervalWaktu;

        const STORAGE_KEY = `CBT_ANS_${JADWAL_ID}_${SISWA_ID}`;
        const TIME_KEY = `CBT_TIME_${JADWAL_ID}_${SISWA_ID}`;

        async function initUjian() {
            try {
                const response = await fetch(JSON_URL);
                if (!response.ok) throw new Error("File JSON tidak ditemukan. Pastikan sudah di-Build.");
                bankSoal = await response.json();

                if (!Array.isArray(bankSoal) || bankSoal.length === 0) throw new Error("Bank soal kosong!");

                const savedData = localStorage.getItem(STORAGE_KEY);
                if (savedData) {
                    jawabanSiswa = JSON.parse(savedData);
                } else {
                    bankSoal.forEach(s => {
                        jawabanSiswa[s.id] = {
                            jawab: null,
                            ragu: false
                        };
                    });
                    simpanState();
                }

                initTimer();
                renderGrid();
                tampilkanSoal(0);
            } catch (err) {
                document.getElementById('kontenSoal').innerHTML = `<div class="bg-red-50 p-6 rounded-lg border border-red-200 text-center"><p class="text-red-600 font-bold text-xl mb-2">Gagal Memuat Ujian!</p><p class="text-red-500">${err.message}</p></div>`;
            }
        }

        function tampilkanSoal(index) {
            if (index < 0 || index >= bankSoal.length) return;
            indexAktif = index;

            const soal = bankSoal[index];
            const state = jawabanSiswa[soal.id] || {
                jawab: null,
                ragu: false
            };
            const teksSoal = soal.pertanyaan || '';
            const isPG = (soal.jenis_soal === 'pg');

            document.getElementById('nomorSoalAktif').innerText = index + 1;
            document.getElementById('badgeJenisSoal').innerText = isPG ? 'PILIHAN GANDA' : 'ESSAI';

            let htmlSoal = `<div class="prose max-w-none text-slate-800 text-lg">${teksSoal}</div>`;
            if (soal.file_audio) {
                htmlSoal += `<div class="mt-5 bg-blue-50 p-3 rounded-xl border border-blue-100 flex w-fit"><audio controls class="h-10 outline-none"><source src="/uploads/audio/${soal.file_audio}" type="audio/mpeg"></audio></div>`;
            }
            document.getElementById('kontenSoal').innerHTML = htmlSoal;

            let htmlJawaban = '';
            if (isPG) {
                htmlJawaban += '<div class="space-y-3">';
                let opsiData = {};
                if (soal.opsi_jawaban) {
                    try {
                        opsiData = typeof soal.opsi_jawaban === 'string' ? JSON.parse(soal.opsi_jawaban) : soal.opsi_jawaban;
                    } catch (e) {
                        console.error(e);
                    }
                }

                const opsiList = ['a', 'b', 'c', 'd', 'e'];
                opsiList.forEach(opt => {
                    const isiOpsi = opsiData[opt] || opsiData[opt.toUpperCase()];
                    if (isiOpsi && isiOpsi.trim() !== '') {
                        const isChecked = state.jawab === opt ? 'checked' : '';
                        const bgClass = isChecked ? 'bg-blue-50 border-blue-400 ring-2 ring-blue-400/30' : 'bg-white border-slate-200 hover:bg-slate-50';

                        htmlJawaban += `
                        <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all shadow-sm ${bgClass}">
                            <input type="radio" name="opsi_${soal.id}" value="${opt}" ${isChecked} onchange="pilihJawaban('${soal.id}', '${opt}')" class="mt-1 w-5 h-5 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            <div class="flex-1 text-base mt-0.5">
                                <span class="font-black text-slate-800 uppercase mr-2 bg-slate-200 px-2 py-0.5 rounded">${opt}</span> 
                                <span class="text-slate-700 font-medium leading-relaxed">${isiOpsi}</span>
                            </div>
                        </label>`;
                    }
                });
                htmlJawaban += '</div>';
            } else {
                htmlJawaban = `<textarea rows="6" placeholder="Ketik jawaban essai Anda di sini..." oninput="debounceJawabanEssai('${soal.id}', this.value)" class="w-full p-4 border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition text-slate-700 shadow-sm">${state.jawab || ''}</textarea>`;
            }
            document.getElementById('areaJawaban').innerHTML = htmlJawaban;

            document.getElementById('checkRagu').checked = state.ragu;
            document.getElementById('btnPrev').disabled = index === 0;

            const btnNext = document.getElementById('btnNext');
            const textNext = document.getElementById('textBtnNext');
            if (index === bankSoal.length - 1) {
                textNext.innerHTML = 'Selesai 🏁';
                btnNext.classList.replace('bg-blue-600', 'bg-emerald-600');
            } else {
                textNext.innerHTML = 'Selanjutnya';
                btnNext.classList.replace('bg-emerald-600', 'bg-blue-600');
            }
            renderGrid();
        }

        function pilihJawaban(id_soal, opsi) {
            jawabanSiswa[id_soal].jawab = opsi;
            simpanState();
            renderGrid();
        }

        let timeoutEssai;

        function debounceJawabanEssai(id_soal, teks) {
            clearTimeout(timeoutEssai);
            timeoutEssai = setTimeout(() => {
                jawabanSiswa[id_soal].jawab = teks;
                simpanState();
                renderGrid();
            }, 500);
        }

        function toggleRagu() {
            const soal = bankSoal[indexAktif];
            jawabanSiswa[soal.id].ragu = document.getElementById('checkRagu').checked;
            simpanState();
            renderGrid();
        }

        function navigasiSoal(arah) {
            if (arah === 'next') {
                if (indexAktif < bankSoal.length - 1) tampilkanSoal(indexAktif + 1);
                else konfirmasiSelesai();
            } else {
                if (indexAktif > 0) tampilkanSoal(indexAktif - 1);
            }
        }

        function renderGrid() {
            let html = '';
            bankSoal.forEach((s, i) => {
                const state = jawabanSiswa[s.id] || {};
                let colorClass = 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50';

                if (state.ragu) colorClass = 'bg-amber-400 text-white border-amber-500 shadow-md shadow-amber-400/30';
                else if (state.jawab && state.jawab.trim() !== '') colorClass = 'bg-emerald-500 text-white border-emerald-600 shadow-md shadow-emerald-500/30';

                const activeRing = i === indexAktif ? 'ring-4 ring-offset-1 ring-blue-500/50 scale-105 z-10' : '';
                html += `<button onclick="tampilkanSoal(${i})" class="w-full aspect-square flex items-center justify-center rounded-lg border-2 font-bold text-sm transition-all duration-200 ${colorClass} ${activeRing}">${i + 1}</button>`;
            });
            document.getElementById('gridNomorSoal').innerHTML = html;
        }

        function simpanState() {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(jawabanSiswa));
        }

        function initTimer() {
            let endTime = localStorage.getItem(TIME_KEY);
            let nowInit = new window.Date().getTime();

            if (!endTime) {
                endTime = nowInit + (DURASI * 60 * 1000);

                // MENCEKIK TIMER JIKA MELEBIHI BATAS TOLERANSI
                if (endTime > ABSOLUTE_DEADLINE) {
                    endTime = ABSOLUTE_DEADLINE;
                    Toastify({
                        text: "⚠️ Waktu ujian Anda dikurangi menyesuaikan batas jam penutupan.",
                        duration: 6000,
                        style: {
                            background: "#f59e0b",
                            borderRadius: "8px"
                        }
                    }).showToast();
                }
                localStorage.setItem(TIME_KEY, endTime);
            } else {
                // Re-validasi jika siswa iseng mengubah memori browser
                if (endTime > ABSOLUTE_DEADLINE) {
                    endTime = ABSOLUTE_DEADLINE;
                    localStorage.setItem(TIME_KEY, endTime);
                }
            }

            intervalWaktu = setInterval(() => {
                const now = new window.Date().getTime();
                const distance = endTime - now;

                if (distance <= 0) {
                    clearInterval(intervalWaktu);
                    document.getElementById('timerDisplay').innerText = "HABIS";
                    forceSubmit("Batas waktu maksimal (Toleransi) telah habis! Jawaban disubmit otomatis...");
                } else {
                    const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((distance % (1000 * 60)) / 1000);

                    document.getElementById('timerDisplay').innerText = (h > 0 ? (h < 10 ? "0" + h : h) + ":" : "") + (m < 10 ? "0" + m : m) + ":" + (s < 10 ? "0" + s : s);
                }
            }, 1000);
        }

        function konfirmasiSelesai() {
            let belumDijawab = 0,
                ragu = 0;
            bankSoal.forEach(s => {
                const state = jawabanSiswa[s.id] || {};
                if (!state.jawab || state.jawab.trim() === '') belumDijawab++;
                if (state.ragu) ragu++;
            });

            let pesan = "Anda yakin ingin mengakhiri ujian ini?";
            if (belumDijawab > 0) pesan = `<div class="p-3 bg-red-50 border border-red-200 rounded text-red-600 font-bold mb-2">Ada ${belumDijawab} soal yang belum dijawab!</div>Tetap kumpulkan?`;
            else if (ragu > 0) pesan = `<div class="p-3 bg-amber-50 border border-amber-200 rounded text-amber-600 font-bold mb-2">Ada ${ragu} jawaban yang masih ragu-ragu!</div>Kumpulkan sekarang?`;

            Swal.fire({
                title: 'Selesai Ujian?',
                html: pesan,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Cek Kembali'
            }).then((result) => {
                if (result.isConfirmed) forceSubmit("Memproses nilai Anda...");
            });
        }

        function forceSubmit(pesanLoading) {
            Swal.fire({
                title: pesanLoading,
                html: 'Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
            clearInterval(intervalWaktu);

            const payload = {
                jadwal_id: JADWAL_ID,
                siswa_id: SISWA_ID,
                jawaban: jawabanSiswa
            };
            document.getElementById('payloadJawaban').value = JSON.stringify(payload);

            localStorage.removeItem(STORAGE_KEY);
            localStorage.removeItem(TIME_KEY);
            document.getElementById('formSubmitUjian').submit();
        }

        window.onload = initUjian;
    </script>
</body>

</html>