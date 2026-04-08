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


namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProdSeeder extends Seeder
{
    public function run()
    {
        $adminData = [
            'username'     => 'admin',
            'password'     => password_hash('admin123', PASSWORD_DEFAULT),
            'nama_lengkap' => 'Administrator',
            'role'         => 'admin',
            'is_panitia'   => 0,
            'created_at'   => date('Y-m-d H:i:s'),
        ];

        if ($this->db->table('staff')->where('username', 'admin')->countAllResults() == 0) {
            $this->db->table('staff')->insert($adminData);
        }

        $jenisUjian = [
            ['nama_ujian' => 'Penilaian Harian (PH)'],
            ['nama_ujian' => 'Penilaian Tengah Semester (PTS)'],
            ['nama_ujian' => 'Penilaian Akhir Semester (PAS)'],
            ['nama_ujian' => 'Ujian Sekolah (US)'],
        ];

        if ($this->db->table('master_jenis_ujian')->countAllResults() == 0) {
            $this->db->table('master_jenis_ujian')->insertBatch($jenisUjian);
        }

        $pengaturan = [
            'nama_sekolah'       => 'Nurindra CBT PRO',
            'kepala_sekolah'     => 'Nurindra, S.Kom, M.Pd, MM',
            'nip_kepala_sekolah' => '-',
            'logo'               => null
        ];

        if ($this->db->table('pengaturan')->countAllResults() == 0) {
            $this->db->table('pengaturan')->insert($pengaturan);
        }
    }
}
