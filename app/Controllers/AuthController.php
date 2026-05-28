<?php

namespace App\Controllers;

use App\Models\StaffModel;
use App\Models\SiswaModel;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    public function index(): ResponseInterface|string
    {
        if (session()->get('logged_in')) {
            return session()->get('user_type') === 'staff'
                ? redirect()->to('/panel/dashboard')
                : redirect()->to('/ujian');
        }

        return view('auth/login');
    }

    public function process(): ResponseInterface
    {
        $username = (string)$this->request->getPost('username');
        $password = (string)$this->request->getPost('password');

        $staffModel = new StaffModel();
        $siswaModel = new SiswaModel();

        // Mengambil properti pengaturan global yang sudah diinisiasi di BaseController (Fase 1)
        $isMaintenance     = (int)($this->pengaturanGlobal['maintenance_mode'] ?? 0);
        $isBlockMultiLogin = (int)($this->pengaturanGlobal['block_multi_login'] ?? 0);

        $staff = $staffModel->where('username', $username)->first();

        if ($staff) {
            if (password_verify($password, (string)$staff['password'])) {
                if ($isMaintenance === 1 && $staff['role'] !== 'admin') {
                    return redirect()->back()->with('error', 'Sistem sedang dalam pemeliharaan (Maintenance). Akses hanya untuk Administrator.');
                }

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

        $siswa = $siswaModel->where('nisn', $username)->first();

        if ($siswa) {
            if (password_verify($password, (string)$siswa['password'])) {
                if ($isMaintenance === 1) {
                    return redirect()->back()->with('error', 'Sistem sedang ditutup sementara oleh Panitia. Silakan tunggu informasi lebih lanjut.');
                }

                if ($isBlockMultiLogin === 1 && (int)$siswa['is_login'] === 1) {
                    return redirect()->back()->with('error', 'Akun sedang aktif di perangkat lain! Lapor Pengawas untuk mereset sesi Anda.');
                }

                $siswaModel->update($siswa['id'], [
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

    public function logout(): ResponseInterface
    {
        $session = session();

        if ($session->get('user_type') === 'siswa') {
            $siswaModel = new SiswaModel();
            $siswaModel->update((string)$session->get('id'), ['is_login' => 0]);
        }

        $session->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar dengan aman.');
    }
}
