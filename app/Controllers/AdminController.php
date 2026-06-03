<?php

namespace App\Controllers;

use App\Models\StaffModel;
use App\Models\PengaturanModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    protected StaffModel $staffModel;
    protected PengaturanModel $pengaturanModel;

    public function __construct()
    {
        $this->staffModel      = new StaffModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    private function checkAdmin(): void
    {
        if (session()->get('role') !== 'admin') {
            // Menggunakan Exception bawaan CI4 untuk menghentikan siklus secara aman jika bukan admin
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Akses Ditolak!');
        }
    }

    public function staff(): string
    {
        $this->checkAdmin();

        $data = [
            'title'        => 'Manajemen Staff - CBT PRO',
            'staff'        => $this->staffModel->orderBy('role', 'ASC')->orderBy('nama_lengkap', 'ASC')->findAll(),
            'totalAdmin'   => $this->staffModel->countByRole('admin'),
            'totalPanitia' => $this->staffModel->countPanitia()
        ];

        return view('panel/manajemen_staff', $data);
    }

    public function storeStaff(): ResponseInterface
    {
        $this->checkAdmin();

        $username  = (string)$this->request->getPost('username');
        $role      = (string)$this->request->getPost('role');
        $isPanitia = $this->request->getPost('is_panitia') ? 1 : 0;

        if ($this->staffModel->where('username', $username)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Username sudah digunakan!');
        }

        if ($role === 'admin') {
            if ($this->staffModel->countByRole('admin') >= 1) {
                return redirect()->back()->with('error', 'Gagal! Admin maksimal hanya boleh 1 orang.');
            }
            $isPanitia = 0;
        }

        if ($isPanitia === 1 && $this->staffModel->countPanitia() >= 3) {
            return redirect()->back()->with('error', 'Gagal! Panitia maksimal hanya boleh 3 orang.');
        }

        $passwordPlain = (string)$this->request->getPost('password') ?: 'password123';

        $this->staffModel->insert([
            'username'     => $username,
            'password'     => password_hash($passwordPlain, PASSWORD_DEFAULT),
            'nama_lengkap' => strtoupper((string)$this->request->getPost('nama_lengkap')),
            'role'         => $role,
            'is_panitia'   => $isPanitia,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Staff baru berhasil ditambahkan.');
    }

    public function updateStaff(string $id): ResponseInterface
    {
        $this->checkAdmin();
        $staffLama = $this->staffModel->find($id);

        if (!$staffLama) {
            return redirect()->back()->with('error', 'Data staff tidak ditemukan.');
        }

        if ($staffLama['username'] === 'admin') {
            $username  = 'admin';
            $role      = 'admin';
            $isPanitia = 0;
        } else {
            $username  = (string)$this->request->getPost('username');
            $role      = (string)($this->request->getPost('role') ?? 'guru');
            $isPanitia = $this->request->getPost('is_panitia') ? 1 : 0;

            // FIX BUG: Validasi duplikasi username (Mengecek semua baris data kecuali ID milik staff ini)
            $usernameBentrok = $this->staffModel->where('username', $username)
                ->where('id !=', $id)
                ->countAllResults();

            if ($usernameBentrok > 0) {
                return redirect()->back()->with('error', "Gagal! Username '{$username}' sudah digunakan oleh staff lain.");
            }

            if ($role === 'admin') {
                if ($this->staffModel->where('role', 'admin')->where('id !=', $id)->countAllResults() >= 1) {
                    return redirect()->back()->with('error', 'Gagal! Admin maksimal hanya boleh 1 orang.');
                }
                $isPanitia = 0;
            }

            if ($isPanitia === 1 && $this->staffModel->where('is_panitia', 1)->where('id !=', $id)->countAllResults() >= 3) {
                return redirect()->back()->with('error', 'Gagal! Panitia maksimal hanya boleh 3 orang.');
            }
        }

        $dataUpdate = [
            'username'     => $username,
            'nama_lengkap' => strtoupper((string)$this->request->getPost('nama_lengkap')),
            'role'         => $role,
            'is_panitia'   => $isPanitia,
        ];

        $passwordBaru = (string)$this->request->getPost('password');
        if (!empty($passwordBaru)) {
            $dataUpdate['password'] = password_hash($passwordBaru, PASSWORD_DEFAULT);
        }

        $this->staffModel->update($id, $dataUpdate);
        return redirect()->back()->with('success', 'Data Staff berhasil diperbarui.');
    }

    public function deleteStaff(string $id): ResponseInterface
    {
        $this->checkAdmin();
        $staffTarget = $this->staffModel->find($id);

        if ($staffTarget['username'] === 'admin') {
            return redirect()->back()->with('error', 'Akses Ditolak! Akun Super Admin utama tidak boleh dihapus.');
        }

        if ($id === (string)session()->get('id')) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri saat sedang login!');
        }

        $this->staffModel->delete($id);
        return redirect()->back()->with('success', 'Akun Staff berhasil dihapus.');
    }

    public function pengaturan(): string
    {
        $this->checkAdmin();

        if ($this->pengaturanModel->countAllResults() === 0) {
            $this->pengaturanModel->insert([
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
            'pengaturan' => $this->pengaturanModel->find(1)
        ];

        return view('panel/pengaturan', $data);
    }

    public function updatePengaturan(): ResponseInterface
    {
        $this->checkAdmin();

        $dataUpdate = [
            'nama_sekolah'       => strtoupper((string)$this->request->getPost('nama_sekolah')),
            'kepala_sekolah'     => (string)$this->request->getPost('kepala_sekolah'),
            'nip_kepala_sekolah' => (string)$this->request->getPost('nip_kepala_sekolah'),
            'alamat_sekolah'     => (string)$this->request->getPost('alamat_sekolah'),
            'email_telepon'      => (string)$this->request->getPost('email_telepon'),
            'tahun_ajaran'       => (string)$this->request->getPost('tahun_ajaran'),
            'semester'           => (string)$this->request->getPost('semester'),
            'zona_waktu'         => (string)$this->request->getPost('zona_waktu'),
            'block_multi_login'  => $this->request->getPost('block_multi_login') ? 1 : 0,
            'maintenance_mode'   => $this->request->getPost('maintenance_mode') ? 1 : 0,
        ];

        $logoFile = $this->request->getFile('logo');

        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {

            $aturanValidasi = [
                'logo' => [
                    'rules'  => 'is_image[logo]|mime_in[logo,image/png,image/jpg,image/jpeg]|max_size[logo,2048]',
                    'errors' => [
                        'is_image' => 'File yang diupload wajib berupa gambar.',
                        'mime_in'  => 'Format gambar hanya diizinkan PNG, JPG, atau JPEG.',
                        'max_size' => 'Ukuran gambar maksimal adalah 2MB.'
                    ]
                ]
            ];

            if (!$this->validate($aturanValidasi)) {
                return redirect()->back()->with('error', $this->validator->getError('logo'));
            }

            $pengaturanLama = $this->pengaturanModel->find(1);
            if ($pengaturanLama && !empty($pengaturanLama['logo'])) {
                $oldPath = FCPATH . 'uploads/' . $pengaturanLama['logo'];
                if (file_exists($oldPath)) unlink($oldPath);
            }

            $newName = $logoFile->getRandomName();
            $logoFile->move(FCPATH . 'uploads', $newName);
            $dataUpdate['logo'] = $newName;
        }

        $this->pengaturanModel->update(1, $dataUpdate);
        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
