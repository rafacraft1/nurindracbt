<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DevSeeder extends Seeder
{
    public function run()
    {
        $this->call('ProdSeeder');

        $passwordDefault = password_hash('password123', PASSWORD_DEFAULT);

        $staffData = [
            [
                'username'     => 'admin',
                'password'     => $passwordDefault,
                'nama_lengkap' => 'Super Administrator',
                'role'         => 'admin',
                'is_panitia'   => 0,
            ],
            [
                'username'     => 'guru1',
                'password'     => $passwordDefault,
                'nama_lengkap' => 'Bapak Budi (Panitia)',
                'role'         => 'guru',
                'is_panitia'   => 1,
            ],
            [
                'username'     => 'guru2',
                'password'     => $passwordDefault,
                'nama_lengkap' => 'Ibu Siti (MTK)',
                'role'         => 'guru',
                'is_panitia'   => 0,
            ],
            [
                'username'     => 'guru3',
                'password'     => $passwordDefault,
                'nama_lengkap' => 'Mr. Jhon (B.Inggris)',
                'role'         => 'guru',
                'is_panitia'   => 0,
            ]
        ];
        $this->db->table('staff')->ignore(true)->insertBatch($staffData);

        $this->db->table('master_jenis_ujian')->ignore(true)->insert(['nama_ujian' => 'Penilaian Tengah Semester (PTS)']);

        $this->db->table('master_mapel')->insertBatch([
            ['nama_mapel' => 'Matematika'],
            ['nama_mapel' => 'Bahasa Inggris']
        ]);

        $this->db->table('ruangan')->insertBatch([
            ['nama_ruangan' => 'Lab Komputer 1'],
            ['nama_ruangan' => 'Ruang Kelas X-A']
        ]);

        $this->db->table('guru_mapel')->insertBatch([
            ['guru_id' => 3, 'mapel_id' => 1],
            ['guru_id' => 4, 'mapel_id' => 2],
        ]);

        $siswaData = [];
        for ($i = 1; $i <= 10; $i++) {
            $siswaData[] = [
                'nisn'           => '1000' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'password'       => password_hash('siswa123', PASSWORD_DEFAULT),
                'password_plain' => 'siswa123',
                'nama_lengkap'   => 'Siswa Dummy ' . $i,
                'tingkat'        => 'XII',
                'jurusan'        => 'RPL',
                'rombel'         => '1',
                'ruangan_id'     => 1,
            ];
        }
        $this->db->table('siswa')->insertBatch($siswaData);

        $soalData = [];
        for ($i = 1; $i <= 10; $i++) {
            $opsi = [
                'a' => 'Pilihan jawaban A untuk soal ' . $i,
                'b' => 'Pilihan jawaban B untuk soal ' . $i,
                'c' => 'Pilihan jawaban C untuk soal ' . $i,
                'd' => 'Pilihan jawaban D untuk soal ' . $i,
                'e' => 'Pilihan jawaban E untuk soal ' . $i,
            ];
            $soalData[] = [
                'mapel_id'      => 1,
                'guru_id'       => 3,
                'jenis_soal'    => 'pg',
                'pertanyaan'    => '<p>Ini adalah contoh soal <b>Pilihan Ganda</b> ke-' . $i . '. Berapakah hasil perhitungan matematika berikut?</p>',
                'opsi_jawaban'  => json_encode($opsi),
                'kunci_jawaban' => 'a',
                'created_at'    => date('Y-m-d H:i:s')
            ];
        }

        for ($i = 1; $i <= 5; $i++) {
            $soalData[] = [
                'mapel_id'      => 1,
                'guru_id'       => 3,
                'jenis_soal'    => 'essai',
                'pertanyaan'    => '<p>Ini adalah contoh soal <b>Essai</b> ke-' . $i . '. Jelaskan langkah-langkah penyelesaian dari rumus matematika tersebut secara rinci!</p>',
                'opsi_jawaban'  => null,
                'kunci_jawaban' => 'Siswa harus dapat menjelaskan minimal 3 langkah penyelesaian dengan logis dan sesuai kaidah dasar matematika.',
                'created_at'    => date('Y-m-d H:i:s')
            ];
        }
        $this->db->table('bank_soal')->insertBatch($soalData);

        $this->db->table('jadwal_ujian')->insert([
            'jenis_ujian_id' => 1,
            'mapel_id'       => 1,
            'tingkat'        => 'XII',
            'jurusan'        => 'RPL',
            'ruangan_id'     => 1,
            'pengawas_id'    => 2,
            'waktu_mulai'    => date('Y-m-d 07:00:00'),
            'waktu_selesai'  => date('Y-m-d 18:00:00'),
            'durasi'         => 90,
            'status'         => 'draft',
            'tahun_ajaran'   => '2025/2026',
            'semester'       => 'ganjil'
        ]);
    }
}
