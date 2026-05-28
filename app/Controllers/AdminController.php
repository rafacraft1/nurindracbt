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


namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\BaseConnection;

class AdminController extends BaseController
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    private function checkAdmin()
    {
        if (session()->get('role') !== 'admin') {
            header('Location: /panel/dashboard');
            exit;
        }
    }

    public function staff()
    {
        $this->checkAdmin();

        $staff = $this->db->table('staff')->orderBy('role', 'ASC')->orderBy('nama_lengkap', 'ASC')->get()->getResultArray();

        $totalAdmin = $this->db->table('staff')->where('role', 'admin')->countAllResults();
        $totalPanitia = $this->db->table('staff')->where('is_panitia', 1)->countAllResults();

        $data = [
            'title'        => 'Manajemen Staff - CBT PRO',
            'staff'        => $staff,
            'totalAdmin'   => $totalAdmin,
            'totalPanitia' => $totalPanitia
        ];

        return view('panel/manajemen_staff', $data);
    }

    public function storeStaff()
    {
        $this->checkAdmin();

        $username  = $this->request->getPost('username');
        $role      = $this->request->getPost('role');
        $isPanitia = $this->request->getPost('is_panitia') ? 1 : 0;

        if ($this->db->table('staff')->where('username', $username)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Username sudah digunakan!');
        }

        if ($role === 'admin') {
            $cekAdmin = $this->db->table('staff')->where('role', 'admin')->countAllResults();
            if ($cekAdmin >= 1) return redirect()->back()->with('error', 'Gagal! Admin maksimal hanya boleh 1 orang.');
            $isPanitia = 0;
        }

        if ($isPanitia == 1) {
            $cekPanitia = $this->db->table('staff')->where('is_panitia', 1)->countAllResults();
            if ($cekPanitia >= 3) return redirect()->back()->with('error', 'Gagal! Panitia maksimal hanya boleh 3 orang.');
        }

        $passwordPlain = $this->request->getPost('password') ?: 'password123';

        $dataInsert = [
            'username'     => $username,
            'password'     => password_hash($passwordPlain, PASSWORD_DEFAULT),
            'nama_lengkap' => strtoupper($this->request->getPost('nama_lengkap')),
            'role'         => $role,
            'is_panitia'   => $isPanitia,
            'created_at'   => date('Y-m-d H:i:s')
        ];

        $this->db->table('staff')->insert($dataInsert);
        return redirect()->back()->with('success', 'Staff baru berhasil ditambahkan.');
    }

    public function updateStaff(string $id)
    {
        $this->checkAdmin();

        $staffLama = $this->db->table('staff')->where('id', $id)->get()->getRowArray();

        if ($staffLama['username'] === 'admin') {
            $username  = 'admin';
            $role      = 'admin';
            $isPanitia = 0;
        } else {
            $username  = $this->request->getPost('username');
            $role      = $this->request->getPost('role') ?? 'guru';
            $isPanitia = $this->request->getPost('is_panitia') ? 1 : 0;

            if ($role === 'admin') {
                $cekAdmin = $this->db->table('staff')->where('role', 'admin')->where('id !=', $id)->countAllResults();
                if ($cekAdmin >= 1) return redirect()->back()->with('error', 'Gagal! Admin maksimal hanya boleh 1 orang.');
                $isPanitia = 0;
            }

            if ($isPanitia == 1) {
                $cekPanitia = $this->db->table('staff')->where('is_panitia', 1)->where('id !=', $id)->countAllResults();
                if ($cekPanitia >= 3) return redirect()->back()->with('error', 'Gagal! Panitia maksimal hanya boleh 3 orang.');
            }
        }

        $dataUpdate = [
            'username'     => $username,
            'nama_lengkap' => strtoupper($this->request->getPost('nama_lengkap')),
            'role'         => $role,
            'is_panitia'   => $isPanitia,
        ];

        $passwordBaru = $this->request->getPost('password');
        if (!empty($passwordBaru)) {
            $dataUpdate['password'] = password_hash($passwordBaru, PASSWORD_DEFAULT);
        }

        $this->db->table('staff')->where('id', $id)->update($dataUpdate);
        return redirect()->back()->with('success', 'Data Staff berhasil diperbarui.');
    }

    public function deleteStaff(string $id)
    {
        $this->checkAdmin();

        $staffTarget = $this->db->table('staff')->where('id', $id)->get()->getRowArray();

        if ($staffTarget['username'] === 'admin') {
            return redirect()->back()->with('error', 'Akses Ditolak! Akun Super Admin utama tidak boleh dihapus.');
        }

        if ($id == session()->get('id')) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri saat sedang login!');
        }

        $this->db->table('staff')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Akun Staff berhasil dihapus.');
    }

    public function pengaturan()
    {
        $this->checkAdmin();

        $builder = $this->db->table('pengaturan');

        if ($builder->countAllResults() == 0) {
            $builder->insert([
                'nama_sekolah'       => 'SMA NEGERI 1 CBT PRO',
                'kepala_sekolah'     => 'Nama Kepala Sekolah, M.Pd',
                'nip_kepala_sekolah' => '198001012005011001',
                'tahun_ajaran'       => '2025/2026',
                'semester'           => 'ganjil',
                'zona_waktu'         => 'Asia/Jakarta',
            ]);
        }

        $data = [
            'title'      => 'Pengaturan Sistem - CBT PRO',
            'pengaturan' => $builder->where('id', 1)->get()->getRowArray()
        ];

        return view('panel/pengaturan', $data);
    }

    public function updatePengaturan()
    {
        $this->checkAdmin();

        $dataUpdate = [
            'nama_sekolah'       => strtoupper($this->request->getPost('nama_sekolah')),
            'kepala_sekolah'     => $this->request->getPost('kepala_sekolah'),
            'nip_kepala_sekolah' => $this->request->getPost('nip_kepala_sekolah'),
            'alamat_sekolah'     => $this->request->getPost('alamat_sekolah'),
            'email_telepon'      => $this->request->getPost('email_telepon'),
            'tahun_ajaran'       => $this->request->getPost('tahun_ajaran'),
            'semester'           => $this->request->getPost('semester'),
            'zona_waktu'         => $this->request->getPost('zona_waktu'),
            'block_multi_login'  => $this->request->getPost('block_multi_login') ? 1 : 0,
            'maintenance_mode'   => $this->request->getPost('maintenance_mode') ? 1 : 0,
        ];

        $logoFile = $this->request->getFile('logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {

            $pengaturanLama = $this->db->table('pengaturan')->where('id', 1)->get()->getRowArray();
            if ($pengaturanLama && $pengaturanLama['logo']) {
                $oldPath = FCPATH . 'uploads/' . $pengaturanLama['logo'];
                if (file_exists($oldPath)) unlink($oldPath);
            }

            $newName = $logoFile->getRandomName();
            $logoFile->move(FCPATH . 'uploads', $newName);
            $dataUpdate['logo'] = $newName;
        }

        $this->db->table('pengaturan')->where('id', 1)->update($dataUpdate);
        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
