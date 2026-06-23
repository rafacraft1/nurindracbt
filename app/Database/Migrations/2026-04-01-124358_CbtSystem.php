<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CbtSystem extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'username'     => ['type' => 'VARCHAR', 'constraint' => 50],
            'password'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'nama_lengkap' => ['type' => 'VARCHAR', 'constraint' => 100],
            'role'         => ['type' => 'ENUM', 'constraint' => ['admin', 'guru'], 'default' => 'guru'],
            'is_panitia'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('staff');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_ujian' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('master_jenis_ujian');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_mapel' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('master_mapel');

        $this->forge->addField([
            'id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'guru_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'mapel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['guru_id', 'mapel_id']);
        $this->forge->createTable('guru_mapel');

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_ruangan' => ['type' => 'VARCHAR', 'constraint' => 50],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ruangan');

        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nisn'           => ['type' => 'VARCHAR', 'constraint' => 20],
            'password'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'password_plain' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'nama_lengkap'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'tingkat'        => ['type' => 'VARCHAR', 'constraint' => 10],
            'jurusan'        => ['type' => 'VARCHAR', 'constraint' => 50],
            'rombel'         => ['type' => 'VARCHAR', 'constraint' => 10],
            'ruangan_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'is_login'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'last_active'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nisn');
        $this->forge->createTable('siswa');

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'mapel_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'guru_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jenis_soal'    => ['type' => 'ENUM', 'constraint' => ['pg', 'essai'], 'default' => 'pg'],
            'pertanyaan'    => ['type' => 'TEXT'],
            'file_audio'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'opsi_jawaban'  => ['type' => 'JSON', 'null' => true],
            'kunci_jawaban' => ['type' => 'TEXT', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('mapel_id');
        $this->forge->createTable('bank_soal');

        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'jenis_ujian_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'mapel_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tingkat'        => ['type' => 'VARCHAR', 'constraint' => 10],
            'jurusan'        => ['type' => 'VARCHAR', 'constraint' => 50],
            'ruangan_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'pengawas_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'acak_soal'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'tampil_nilai'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'waktu_mulai'    => ['type' => 'DATETIME'],
            'waktu_selesai'  => ['type' => 'DATETIME'],
            'durasi'         => ['type' => 'INT', 'constraint' => 5],
            'status'         => ['type' => 'ENUM', 'constraint' => ['draft', 'ready', 'active', 'finished'], 'default' => 'draft'],
            'tahun_ajaran'   => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => '2025/2026'],
            'semester'       => ['type' => 'ENUM', 'constraint' => ['ganjil', 'genap'], 'default' => 'ganjil'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('ruangan_id');
        $this->forge->addKey(['tahun_ajaran', 'semester']);
        $this->forge->createTable('jadwal_ujian');

        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'jadwal_id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'siswa_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'soal_acak_pg'        => ['type' => 'JSON', 'null' => true],
            'soal_acak_essai'     => ['type' => 'JSON', 'null' => true],
            'jawaban_peserta'     => ['type' => 'JSON', 'null' => true],
            'nilai_pg'            => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'nilai_essai'         => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'cheat_count'         => ['type' => 'INT', 'constraint' => 3, 'default' => 0],
            'is_hadir'            => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'status'              => ['type' => 'ENUM', 'constraint' => ['pending', 'progress', 'completed', 'locked'], 'default' => 'pending'],
            'waktu_mulai_ujian'   => ['type' => 'DATETIME', 'null' => true],
            'waktu_selesai_ujian' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('jadwal_id');
        $this->forge->addKey('siswa_id');
        $this->forge->addKey(['jadwal_id', 'siswa_id']);
        $this->forge->createTable('hasil_ujian');

        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'constraint' => 1, 'unsigned' => true, 'auto_increment' => true],
            'nama_sekolah'       => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'Nurindra CBT PRO'],
            'kepala_sekolah'     => ['type' => 'VARCHAR', 'constraint' => 150, 'default' => 'Nurindra, S.Kom., M.Pd., MM'],
            'nip_kepala_sekolah' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => '198001012005011001'],
            'logo'               => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'alamat_sekolah'     => ['type' => 'TEXT', 'null' => true],
            'email_telepon'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tahun_ajaran'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => '2025/2026'],
            'semester'           => ['type' => 'ENUM', 'constraint' => ['ganjil', 'genap'], 'default' => 'ganjil'],
            'zona_waktu'         => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Asia/Jakarta'],
            'block_multi_login'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'maintenance_mode'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pengaturan');
    }

    public function down()
    {
        $this->forge->dropTable('pengaturan', true);
        $this->forge->dropTable('hasil_ujian', true);
        $this->forge->dropTable('jadwal_ujian', true);
        $this->forge->dropTable('bank_soal', true);
        $this->forge->dropTable('siswa', true);
        $this->forge->dropTable('ruangan', true);
        $this->forge->dropTable('guru_mapel', true);
        $this->forge->dropTable('master_mapel', true);
        $this->forge->dropTable('master_jenis_ujian', true);
        $this->forge->dropTable('staff', true);
    }
}
