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

class PengawasController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // 1. Menampilkan Daftar Jadwal Mengawas
    public function index()
    {
        $role       = session()->get('role');
        $pengawasId = session()->get('id');

        $builder = $this->db->table('jadwal_ujian')
            ->select('jadwal_ujian.*, master_mapel.nama_mapel, ruangan.nama_ruangan')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id', 'left')
            ->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id', 'left');

        if ($role !== 'admin') {
            $builder->where('pengawas_id', $pengawasId);
        }

        $jadwal = $builder->orderBy('waktu_mulai', 'DESC')->get()->getResultArray();

        $data = [
            'title'  => 'Daftar Ruang Pengawas - CBT PRO',
            'jadwal' => $jadwal
        ];

        return view('panel/pengawas_index', $data);
    }

    public function monitor($jadwalId)
    {
        $jadwal = $this->db->table('jadwal_ujian')
            ->select('jadwal_ujian.*, master_mapel.nama_mapel, ruangan.nama_ruangan')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id')
            ->join('ruangan', 'ruangan.id = jadwal_ujian.ruangan_id')
            ->where('jadwal_ujian.id', $jadwalId)
            ->get()->getRowArray();

        if (!$jadwal) return redirect()->to('/panel/ruang-pengawas')->with('error', 'Jadwal tidak ditemukan.');

        if (session()->get('role') !== 'admin' && $jadwal['pengawas_id'] != session()->get('id')) {
            return redirect()->to('/panel/ruang-pengawas')->with('error', 'Akses Ditolak! Anda bukan pengawas di ruangan ini.');
        }

        $siswa = $this->db->table('siswa')
            ->select('siswa.id, siswa.nisn, siswa.nama_lengkap, siswa.is_login, hasil_ujian.status as status_ujian, hasil_ujian.is_hadir')
            ->join('hasil_ujian', "hasil_ujian.siswa_id = siswa.id AND hasil_ujian.jadwal_id = $jadwalId", 'left')
            ->where('siswa.ruangan_id', $jadwal['ruangan_id'])
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()->getResultArray();

        $currentToken   = 'BELUM ADA';
        $sisaWaktuDetik = 900;

        $jsonPath = FCPATH . 'data_ruangan/token_' . $jadwalId . '.json';
        if (file_exists($jsonPath)) {
            $tokenData    = json_decode(file_get_contents($jsonPath), true);
            $currentToken = $tokenData['token'] ?? 'BELUM ADA';

            if (isset($tokenData['updated_at'])) {
                $elapsed = time() - strtotime($tokenData['updated_at']);
                $sisaWaktuDetik = 900 - $elapsed;

                if ($sisaWaktuDetik < 0) {
                    $sisaWaktuDetik = 0;
                }
            }
        }

        $data = [
            'title'      => 'Monitoring Ruangan - CBT PRO',
            'jadwal'     => $jadwal,
            'siswa'      => $siswa,
            'token'      => $currentToken,
            'sisa_waktu' => $sisaWaktuDetik
        ];

        return view('panel/pengawas_monitor', $data);
    }

    public function generateTokenAjax($jadwalId)
    {
        $token = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);

        $jsonContent = json_encode([
            'token'      => $token,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $filePath = FCPATH . 'data_ruangan/token_' . $jadwalId . '.json';

        if (file_put_contents($filePath, $jsonContent)) {
            $jadwal = $this->db->table('jadwal_ujian')->where('id', $jadwalId)->get()->getRowArray();
            if ($jadwal['status'] == 'ready') {
                $this->db->table('jadwal_ujian')->where('id', $jadwalId)->update(['status' => 'active']);
            }

            return $this->response->setJSON([
                'success'  => true,
                'token'    => $token,
                'csrfHash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menulis file token!']);
    }

    public function resetLogin($siswaId)
    {
        $this->db->table('siswa')->where('id', $siswaId)->update(['is_login' => 0]);
        return redirect()->back()->with('success', 'Sesi login siswa berhasil direset. Siswa sudah bisa login kembali.');
    }

    public function forceSelesai($jadwalId, $siswaId)
    {
        $this->db->table('hasil_ujian')
            ->where('jadwal_id', $jadwalId)
            ->where('siswa_id', $siswaId)
            ->update(['status' => 'completed']);

        return redirect()->back()->with('success', 'Ujian siswa berhasil diselesaikan secara paksa.');
    }

    public function tandaiHadir($jadwalId, $siswaId)
    {
        $cek = $this->db->table('hasil_ujian')
            ->where(['jadwal_id' => $jadwalId, 'siswa_id' => $siswaId])
            ->get()->getRowArray();

        if ($cek) {
            $newHadir = $cek['is_hadir'] == 1 ? 0 : 1;
            $this->db->table('hasil_ujian')->where('id', $cek['id'])->update(['is_hadir' => $newHadir]);
            return $this->response->setJSON(['success' => true, 'is_hadir' => $newHadir, 'csrfHash' => csrf_hash()]);
        } else {
            $this->db->table('hasil_ujian')->insert([
                'jadwal_id' => $jadwalId,
                'siswa_id'  => $siswaId,
                'is_hadir'  => 1,
                'status'    => 'pending'
            ]);
            return $this->response->setJSON(['success' => true, 'is_hadir' => 1, 'csrfHash' => csrf_hash()]);
        }
    }
}
