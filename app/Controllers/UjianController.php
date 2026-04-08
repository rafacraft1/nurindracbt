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

class UjianController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // 1. Halaman Lobi Siswa (Menampilkan Jadwal Tersedia)
    public function index()
    {
        $siswa = session()->get();
        $now   = date('Y-m-d H:i:s');

        $this->db->table('jadwal_ujian')
            ->where('waktu_selesai <=', $now)
            ->whereIn('status', ['ready', 'active'])
            ->update(['status' => 'finished']);

        $riwayat = $this->db->table('hasil_ujian')
            ->where('siswa_id', $siswa['id'])
            ->get()->getResultArray();

        $statusUjian    = [];
        $kehadiran      = [];
        $jadwalProgress = [];

        foreach ($riwayat as $r) {
            $statusUjian[$r['jadwal_id']] = $r['status'];
            $kehadiran[$r['jadwal_id']]   = $r['is_hadir'];
            if ($r['status'] === 'progress') {
                $jadwalProgress[] = $r['jadwal_id'];
            }
        }

        $builder = $this->db->table('jadwal_ujian')
            ->select('jadwal_ujian.*, master_mapel.nama_mapel, master_jenis_ujian.nama_ujian')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id')
            ->join('master_jenis_ujian', 'master_jenis_ujian.id = jadwal_ujian.jenis_ujian_id')
            ->where('jadwal_ujian.ruangan_id', $siswa['ruangan_id'])
            ->where('jadwal_ujian.tingkat', $siswa['tingkat'])
            ->where('jadwal_ujian.jurusan', $siswa['jurusan']);

        if (!empty($jadwalProgress)) {
            $builder->groupStart()
                ->whereIn('jadwal_ujian.status', ['ready', 'active'])
                ->orWhereIn('jadwal_ujian.id', $jadwalProgress)
                ->groupEnd();
        } else {
            $builder->whereIn('jadwal_ujian.status', ['ready', 'active']);
        }

        $jadwalAktif = $builder->orderBy('jadwal_ujian.waktu_mulai', 'ASC')->get()->getResultArray();

        $data = [
            'title'       => 'Lobi Ujian - CBT PRO',
            'jadwalAktif' => $jadwalAktif,
            'statusUjian' => $statusUjian,
            'kehadiran'   => $kehadiran
        ];

        return view('ujian/index', $data);
    }

    public function mulai()
    {
        $jadwalId   = $this->request->getPost('jadwal_id');
        $tokenInput = strtoupper($this->request->getPost('token'));
        $siswaId    = session()->get('id');

        $jadwal = $this->db->table('jadwal_ujian')->where('id', $jadwalId)->get()->getRowArray();

        if ($jadwal['waktu_selesai'] <= date('Y-m-d H:i:s')) {
            return redirect()->back()->with('error', 'Akses Ditolak! Jadwal ujian ini sudah ditutup.');
        }

        $jsonPath = FCPATH . 'data_ruangan/token_' . $jadwalId . '.json';
        if (!file_exists($jsonPath)) return redirect()->back()->with('error', 'Token belum dirilis oleh Pengawas!');

        $tokenData = json_decode(file_get_contents($jsonPath), true);
        if ($tokenInput !== $tokenData['token']) return redirect()->back()->with('error', 'Token salah atau sudah kadaluarsa!');

        $cekHasil = $this->db->table('hasil_ujian')
            ->where('jadwal_id', $jadwalId)
            ->where('siswa_id', $siswaId)
            ->get()->getRowArray();

        if (!$cekHasil || $cekHasil['is_hadir'] == 0) {
            return redirect()->back()->with('error', 'Akses Ditolak! Anda belum diabsen kehadiran oleh Pengawas di dalam ruangan.');
        }

        if ($cekHasil['status'] == 'completed') {
            return redirect()->back()->with('error', 'Anda sudah menyelesaikan ujian ini!');
        }

        if ($cekHasil['status'] == 'pending') {
            $this->db->table('hasil_ujian')->where('id', $cekHasil['id'])->update([
                'status'            => 'progress',
                'waktu_mulai_ujian' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to('/ujian/kerjakan/' . $jadwalId);
    }

    public function kerjakan($jadwalId)
    {
        $siswaId = session()->get('id');

        $hasil = $this->db->table('hasil_ujian')->where('jadwal_id', $jadwalId)->where('siswa_id', $siswaId)->get()->getRowArray();
        if (!$hasil || $hasil['status'] !== 'progress') {
            return redirect()->to('/ujian')->with('error', 'Akses ilegal. Silakan masukkan token terlebih dahulu.');
        }

        $jadwal = $this->db->table('jadwal_ujian')
            ->select('jadwal_ujian.*, master_mapel.nama_mapel')
            ->join('master_mapel', 'master_mapel.id = jadwal_ujian.mapel_id')
            ->where('jadwal_ujian.id', $jadwalId)
            ->get()->getRowArray();

        $absoluteDeadline = strtotime($jadwal['waktu_selesai']) + (15 * 60);

        if (time() > $absoluteDeadline) {
            $this->db->table('hasil_ujian')->where('id', $hasil['id'])->update(['status' => 'completed']);
            $this->db->table('siswa')->where('id', $siswaId)->update(['is_login' => 0]);

            return redirect()->to('/ujian')->with('error', 'Waktu toleransi pengerjaan (15 Menit) telah habis. Jawaban Anda disubmit otomatis.');
        }

        $data = [
            'title'  => 'Mengerjakan: ' . $jadwal['nama_mapel'],
            'jadwal' => $jadwal,
            'hasil'  => $hasil
        ];

        return view('ujian/kerjakan', $data);
    }

    public function submit()
    {
        $payloadJson = $this->request->getPost('payload_jawaban');
        if (!$payloadJson) return redirect()->to('/ujian')->with('error', 'Gagal mengirim jawaban. Data kosong.');

        $payload      = json_decode($payloadJson, true);
        $jadwalId     = $payload['jadwal_id'];
        $siswaId      = $payload['siswa_id'];
        $jawabanSiswa = $payload['jawaban'];

        $jadwal = $this->db->table('jadwal_ujian')->where('id', $jadwalId)->get()->getRowArray();
        $bankSoal = $this->db->table('bank_soal')->where('mapel_id', $jadwal['mapel_id'])->get()->getResultArray();

        $kunciAsli = [];
        foreach ($bankSoal as $s) {
            $kunciAsli[$s['id']] = $s;
        }

        $benar      = 0;
        $totalGanda = 0;

        foreach ($jawabanSiswa as $idSoal => $data) {
            $soalDB = $kunciAsli[$idSoal] ?? null;
            if (!$soalDB) continue;

            if ($soalDB['jenis_soal'] === 'pg') {
                $totalGanda++;
                if (!empty($data['jawab']) && strtolower($data['jawab']) === strtolower($soalDB['kunci_jawaban'])) {
                    $benar++;
                }
            }
        }

        $nilai_pg = $totalGanda > 0 ? ($benar / $totalGanda) * 100 : 0;

        $this->db->table('hasil_ujian')
            ->where('jadwal_id', $jadwalId)
            ->where('siswa_id', $siswaId)
            ->update([
                'jawaban_peserta'     => json_encode($jawabanSiswa),
                'nilai_pg'            => round($nilai_pg, 2),
                'status'              => 'completed',
                'waktu_selesai_ujian' => date('Y-m-d H:i:s')
            ]);

        $this->db->table('siswa')->where('id', $siswaId)->update(['is_login' => 0]);

        return redirect()->to('/ujian')->with('success', 'Selamat! Ujian berhasil diselesaikan dan Nilai telah direkam.');
    }
}
