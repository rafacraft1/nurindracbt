<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DevSeeder extends Seeder
{
    public function run()
    {
        $passwordDefault = password_hash('password123', PASSWORD_DEFAULT);

        // 1. Insert 1 Admin Absolute & 3 Guru (1 di antaranya delegasi Panitia)
        $staffData = [
            [
                'username'     => 'admin',
                'password'     => $passwordDefault,
                'nama_lengkap' => 'Super Administrator',
                'role'         => 'admin',
                'is_panitia'   => 0, // Admin otomatis punya semua akses, tidak perlu flag panitia
            ],
            [
                'username'     => 'guru1', // Jadi Panitia
                'password'     => $passwordDefault,
                'nama_lengkap' => 'Bapak Budi (Panitia)',
                'role'         => 'guru',
                'is_panitia'   => 1,
            ],
            [
                'username'     => 'guru2', // Guru Biasa
                'password'     => $passwordDefault,
                'nama_lengkap' => 'Ibu Siti (MTK)',
                'role'         => 'guru',
                'is_panitia'   => 0,
            ],
            [
                'username'     => 'guru3', // Guru Biasa
                'password'     => $passwordDefault,
                'nama_lengkap' => 'Mr. Jhon (B.Inggris)',
                'role'         => 'guru',
                'is_panitia'   => 0,
            ]
        ];
        $this->db->table('staff')->insertBatch($staffData);

        // 2. Insert Master Data (Jenis Ujian & Mapel & Ruangan)
        $this->db->table('master_jenis_ujian')->insert(['nama_ujian' => 'Penilaian Tengah Semester (PTS)']);

        $this->db->table('master_mapel')->insertBatch([
            ['nama_mapel' => 'Matematika'],
            ['nama_mapel' => 'Bahasa Inggris']
        ]);

        $this->db->table('ruangan')->insertBatch([
            ['nama_ruangan' => 'Lab Komputer 1'],
            ['nama_ruangan' => 'Ruang Kelas X-A']
        ]);

        // 3. Relasikan Guru dengan Mapel (Pivot)
        $this->db->table('guru_mapel')->insertBatch([
            ['guru_id' => 3, 'mapel_id' => 1], // Ibu Siti -> MTK
            ['guru_id' => 4, 'mapel_id' => 2], // Mr. Jhon -> B.Inggris
        ]);

        // 4. Insert 10 Siswa Dummy di Lab Komputer 1
        $siswaData = [];
        for ($i = 1; $i <= 10; $i++) {
            $siswaData[] = [
                'nisn'         => '1000' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'password'     => password_hash('siswa123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Siswa Dummy ' . $i,
                'tingkat'      => 'XII',
                'jurusan'      => 'RPL',
                'rombel'       => '1',
                'ruangan_id'   => 1,
            ];
        }
        $this->db->table('siswa')->insertBatch($siswaData);

        // =========================================================
        // 5. INSERT 15 SOAL DUMMY (10 PG + 5 ESSAI) UNTUK MATEMATIKA
        // =========================================================
        $soalData = [];

        // --- 10 Soal Pilihan Ganda ---
        for ($i = 1; $i <= 10; $i++) {
            $opsi = [
                'a' => 'Pilihan jawaban A untuk soal ' . $i,
                'b' => 'Pilihan jawaban B untuk soal ' . $i,
                'c' => 'Pilihan jawaban C untuk soal ' . $i,
                'd' => 'Pilihan jawaban D untuk soal ' . $i,
                'e' => 'Pilihan jawaban E untuk soal ' . $i,
            ];
            $soalData[] = [
                'mapel_id'      => 1, // Matematika
                'guru_id'       => 3, // Ibu Siti
                'jenis_soal'    => 'pg',
                'pertanyaan'    => '<p>Ini adalah contoh soal <b>Pilihan Ganda</b> ke-' . $i . '. Berapakah hasil perhitungan matematika berikut?</p>',
                'opsi_jawaban'  => json_encode($opsi),
                'kunci_jawaban' => 'a', // Semua kunci jawaban kita set 'A' untuk testing
                'created_at'    => date('Y-m-d H:i:s')
            ];
        }

        // --- 5 Soal Essai ---
        for ($i = 1; $i <= 5; $i++) {
            $soalData[] = [
                'mapel_id'      => 1, // Matematika
                'guru_id'       => 3, // Ibu Siti
                'jenis_soal'    => 'essai',
                'pertanyaan'    => '<p>Ini adalah contoh soal <b>Essai</b> ke-' . $i . '. Jelaskan langkah-langkah penyelesaian dari rumus matematika tersebut secara rinci!</p>',
                'opsi_jawaban'  => null, // Essai tidak memiliki opsi
                'kunci_jawaban' => 'Siswa harus dapat menjelaskan minimal 3 langkah penyelesaian dengan logis dan sesuai kaidah dasar matematika.', // Acuan/Rubrik untuk Guru
                'created_at'    => date('Y-m-d H:i:s')
            ];
        }
        $this->db->table('bank_soal')->insertBatch($soalData);

        // =========================================================
        // 6. INSERT JADWAL UJIAN DUMMY (Sesuai Migration Terbaru)
        // =========================================================
        $this->db->table('jadwal_ujian')->insert([
            'jenis_ujian_id' => 1,
            'mapel_id'       => 1, // Matematika
            'tingkat'        => 'XII',
            'jurusan'        => 'RPL',
            'ruangan_id'     => 1, // Lab Komputer 1
            'pengawas_id'    => 2, // Bapak Budi (Panitia)
            'waktu_mulai'    => date('Y-m-d 07:00:00'), // Ujian dibuka jam 07:00
            'waktu_selesai'  => date('Y-m-d 18:00:00'), // Ujian ditutup jam 18:00
            'durasi'         => 90, // Timer pengerjaan 90 menit di layar siswa
            'status'         => 'draft' // Diset 'draft' agar Admin/Panitia harus mengklik tombol 'Build JSON' terlebih dahulu
        ]);
    }
}
