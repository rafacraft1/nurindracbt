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

class AuthController extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return session()->get('user_type') === 'staff'
                ? redirect()->to('/panel/dashboard')
                : redirect()->to('/ujian'); // <-- PERBAIKAN: Lempar ke /ujian, bukan /
        }

        return view('auth/login');
    }

    public function process()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $db = \Config\Database::connect();

        $staff = $db->table('staff')->where('username', $username)->get()->getRowArray();

        if ($staff) {
            if (password_verify($password, $staff['password'])) {
                session()->set([
                    'id'           => $staff['id'],
                    'username'     => $staff['username'],
                    'nama_lengkap' => $staff['nama_lengkap'],
                    'role'         => $staff['role'],
                    'is_panitia'   => $staff['is_panitia'],
                    'user_type'    => 'staff',
                    'logged_in'    => true
                ]);
                return redirect()->to('/panel/dashboard')->with('success', 'Selamat datang kembali, ' . $staff['nama_lengkap']);
            }
            return redirect()->back()->with('error', 'Password Staff salah.');
        }

        $siswa = $db->table('siswa')->where('nisn', $username)->get()->getRowArray();

        if ($siswa) {
            if (password_verify($password, $siswa['password'])) {

                if ($siswa['is_login'] == 1) {
                    return redirect()->back()->with('error', 'Akun sedang aktif di perangkat lain! Lapor Pengawas untuk mereset sesi Anda.');
                }

                $db->table('siswa')->where('id', $siswa['id'])->update([
                    'is_login'    => 1,
                    'last_active' => date('Y-m-d H:i:s')
                ]);

                session()->set([
                    'id'           => $siswa['id'],
                    'nisn'         => $siswa['nisn'],
                    'nama_lengkap' => $siswa['nama_lengkap'],
                    'tingkat'      => $siswa['tingkat'],
                    'jurusan'      => $siswa['jurusan'],
                    'ruangan_id'   => $siswa['ruangan_id'],
                    'user_type'    => 'siswa',
                    'logged_in'    => true
                ]);

                return redirect()->to('/ujian')->with('success', 'Berhasil login. Selamat mengerjakan ujian.');
            }
            return redirect()->back()->with('error', 'Password Siswa salah.');
        }

        return redirect()->back()->with('error', 'Username / NISN tidak terdaftar di sistem.');
    }

    public function logout()
    {
        $session = session();

        if ($session->get('user_type') === 'siswa') {
            $db = \Config\Database::connect();
            $db->table('siswa')->where('id', $session->get('id'))->update(['is_login' => 0]);
        }

        $session->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar dengan aman.');
    }
}
