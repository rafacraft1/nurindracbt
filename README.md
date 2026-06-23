# 🎓 CBT PRO - ENTERPRISE EDITION

**Versi 2.0.0** | **© 2026 Nurindra CBT PRO**

![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-EE4323?logo=codeigniter&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?logo=tailwind-css&logoColor=white)
![License](https://img.shields.io/badge/License-Freeware-green)

**CBT PRO** adalah platform Ujian Berbasis Komputer (*Computer Based Test*) berskala Enterprise yang dirancang untuk performa tinggi, keamanan absolut, dan manajemen akademik terintegrasi untuk institusi modern.

> 📢 **LISENSI & PENGGUNAAN:**
> Aplikasi ini boleh digunakan dan disebarluaskan secara **GRATIS**.

---

## ✨ Fitur Unggulan (Enterprise-Grade)

* 🚀 **High Performance Architecture:** Menggunakan *AJAX Chunking* & *JSONL Streams* untuk import ribuan data Excel tanpa membebani RAM server, serta *Session Unblocking* agar ujian siswa 100% bebas *lag*.
* 🛡️ **Advanced Security:** Terlindungi dari *DDoS/Spam* via Global Throttle (Rate Limiting), proteksi *Stored XSS*, *Zip Slip* (Path Traversal), hingga *Excel Formula Injection*.
* 🚫 **Sistem Anti-Curang (Strict CBT):**
  * Sinkronisasi jam absolut server (kebal manipulasi jam/waktu di laptop siswa).
  * Deteksi *Visibility Change* (Peringatan saat siswa pindah tab/minimize browser).
  * Pencegahan klik kanan & *Inspect Element*.
* 🖨️ **Smart Print Layout:** Cetak Kartu Siswa (Landscape) & ID Card Pengawas (Portrait) yang 100% presisi untuk kertas A4 tanpa *blank page* atau elemen terpotong.
* 💾 **Disaster Recovery:** Modul *Backup & Restore Database* (beserta aset media) dan *Factory Reset* satu klik dari panel Admin.

---

## 🛠️ Persyaratan Sistem (Prerequisites)

Pastikan server (VPS/Shared Hosting) Anda memenuhi spesifikasi berikut:

* **PHP:** Versi 8.1 atau yang lebih baru.
* **Database:** MySQL 5.7+ / MariaDB 10.3+
* **Ekstensi PHP Wajib:** `intl`, `mbstring`, `gd`, `zip`, `curl`, `json`, `mysqlnd`
* **Web Server:** Apache atau Nginx

---

## 🚀 Panduan Instalasi & Deployment

### Langkah 1: Clone Repositori

Unduh atau *clone* repositori ini ke dalam server Anda:

```bash
git clone https://github.com/rafacraft1/nurindracbt

cd nurindracbt
```

### Langkah 2: Install Dependensi (Composer)

Jalankan perintah berikut untuk mengunduh pustaka:

```bash
composer install --no-dev --optimize-autoloader
```

### Langkah 3: Konfigurasi Environment (.env)

1. Salin file env bawaan menjadi .env:

    ```bash
    cp env .env
    ```

2. Buka dan edit file .env. Sesuaikan parameter berikut:

    ```text
    CI_ENVIRONMENT = production

    app.baseURL = 'https://ujian.sekolahanda.com/'
    app.forceGlobalSecureRequests = true # Ubah ke true jika memakai HTTPS/SSL

    database.default.hostname = localhost
    database.default.database = nama_database_anda
    database.default.username = user_database_anda
    database.default.password = password_database_anda
    database.default.DBDriver = MySQLi
    ```

### Langkah 4: Set Permission Folder

Folder writable/ dan public/uploads/ wajib memiliki akses tulis:

```bash
chmod -R 775 writable/
chmod -R 775 public/uploads/
```

### Langkah 5: Migrasi Database & Seeding Awal

Bangun struktur database dan isi data bawaan:

```bash
php spark migrate
php spark db:seed ProdSeeder
```

### Langkah 6: Migrasi Database & Seeding Awal

Konfigurasi Web Server (Document Root)

Arahkan Document Root pada pengaturan web server/domain Anda ke dalam folder public/. (Contoh: /var/www/nurindracbt/public atau /home/user/public_html/cbt/public).

## 🔑 Akses Login Default

Setelah instalasi berhasil, Anda dapat login menggunakan kredensial bawaan:

* Role: Super Administrator
* Username: admin
* Password: admin123

(Sangat disarankan untuk segera mengubah password administrator melalui menu Manajemen Staff setelah instalasi berhasil).

## 👨‍💻 Pengembang & Dukungan

Dikembangkan dengan dedikasi penuh oleh Nurindra.

Jika Anda membutuhkan bantuan instalasi, pelaporan bug, atau ingin mendiskusikan kustomisasi fitur tambahan, silakan hubungi melalui saluran berikut:

* 📧 Email: <nurindra.id@gmail.com>
* 💬 WhatsApp: +62 812-2032-9780
* 📺 YouTube: Nurindra ID
