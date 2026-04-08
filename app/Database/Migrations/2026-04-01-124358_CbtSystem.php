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
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nisn'         => ['type' => 'VARCHAR', 'constraint' => 20],
            'password'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'nama_lengkap' => ['type' => 'VARCHAR', 'constraint' => 100],
            'tingkat'      => ['type' => 'VARCHAR', 'constraint' => 10],
            'jurusan'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'rombel'       => ['type' => 'VARCHAR', 'constraint' => 10],
            'ruangan_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'is_login'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'last_active'  => ['type' => 'DATETIME', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
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
        $this->forge->createTable('bank_soal');
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'jenis_ujian_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'mapel_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tingkat'        => ['type' => 'VARCHAR', 'constraint' => 10],
            'jurusan'        => ['type' => 'VARCHAR', 'constraint' => 50],
            'ruangan_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'pengawas_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'waktu_mulai'    => ['type' => 'DATETIME', 'comment' => 'Jam awal ujian dibuka (Misal: 10.00)'],
            'waktu_selesai'  => ['type' => 'DATETIME', 'comment' => 'Jam akhir ujian ditutup (Misal: 15.00)'],
            'durasi'         => ['type' => 'INT', 'constraint' => 5, 'comment' => 'Lama pengerjaan dalam menit (Misal: 90)'],
            'status'         => ['type' => 'ENUM', 'constraint' => ['draft', 'ready', 'active', 'finished'], 'default' => 'draft'],
        ]);
        $this->forge->addKey('id', true);
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
            'is_hadir'            => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'comment' => 'Absensi fisik oleh pengawas'],
            'status'              => ['type' => 'ENUM', 'constraint' => ['pending', 'progress', 'completed', 'locked'], 'default' => 'pending'],
            'waktu_mulai_ujian'   => ['type' => 'DATETIME', 'null' => true, 'comment' => 'Waktu rill siswa klik mulai'],
            'waktu_selesai_ujian' => ['type' => 'DATETIME', 'null' => true, 'comment' => 'Waktu rill jawaban tersubmit'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('hasil_ujian');
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'constraint' => 1, 'unsigned' => true, 'auto_increment' => true],
            'nama_sekolah'       => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'Nurindra CBT PRO'],
            'kepala_sekolah'     => ['type' => 'VARCHAR', 'constraint' => 150, 'default' => 'Nurindra, S.Kom., M.Pd., MM'],
            'nip_kepala_sekolah' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => '198001012005011001'],
            'logo'               => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
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
