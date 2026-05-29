<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setAutoRoute(false);
$routes->get('/', 'AuthController::index');
$routes->get('login', 'AuthController::index');
$routes->post('auth/process', 'AuthController::process');
$routes->get('logout', 'AuthController::logout');

$routes->group('', ['filter' => 'auth:siswa'], static function ($routes) {
    $routes->get('ujian', 'UjianController::index');
    $routes->post('ujian/mulai', 'UjianController::mulai');
    $routes->get('ujian/kerjakan/(:num)', 'UjianController::kerjakan/$1');
    $routes->post('ujian/submit', 'UjianController::submit');
});

$routes->group('panel', ['filter' => 'auth'], static function ($routes) {

    $routes->get('dashboard', 'PanelController::dashboard');

    $routes->get('manajemen-staff', 'AdminController::staff');
    $routes->post('manajemen-staff/store', 'AdminController::storeStaff');
    $routes->post('manajemen-staff/update/(:num)', 'AdminController::updateStaff/$1');
    $routes->post('manajemen-staff/delete/(:num)', 'AdminController::deleteStaff/$1');

    $routes->get('pengaturan', 'AdminController::pengaturan');
    $routes->post('pengaturan/update', 'AdminController::updatePengaturan');

    // Modul Backup & Restore (Admin Only)
    $routes->get('backup-restore', 'BackupController::index');
    $routes->get('backup-restore/download/(:segment)', 'BackupController::download/$1');
    $routes->post('backup-restore/restore', 'BackupController::restore');
    $routes->post('backup-restore/factory-reset', 'BackupController::factoryReset');

    $routes->group('', ['filter' => 'auth:panitia'], static function ($routes) {

        $routes->get('siswa', 'SiswaController::index');
        $routes->post('siswa/store', 'SiswaController::store');
        $routes->post('siswa/update/(:num)', 'SiswaController::update/$1');
        $routes->post('siswa/delete-batch', 'SiswaController::deleteBatch');
        $routes->post('siswa/delete/(:num)', 'SiswaController::delete/$1');
        $routes->post('siswa/import', 'SiswaController::import');

        $routes->get('ruangan', 'RuanganController::index');
        $routes->post('ruangan/store', 'RuanganController::store');
        $routes->post('ruangan/delete/(:num)', 'RuanganController::delete/$1');
        $routes->post('ruangan/plot-siswa', 'RuanganController::plotSiswa');
        $routes->post('ruangan/kosongkan/(:num)', 'RuanganController::kosongkan/$1');

        $routes->get('mapel', 'MapelController::index');
        $routes->post('mapel/store', 'MapelController::store');
        $routes->post('mapel/update/(:num)', 'MapelController::update/$1');
        $routes->post('mapel/delete/(:num)', 'MapelController::delete/$1');
        $routes->post('mapel/sync-guru', 'MapelController::syncGuru');

        $routes->get('jadwal', 'JadwalController::index');
        $routes->post('jadwal/store', 'JadwalController::store');
        $routes->post('jadwal/update/(:num)', 'JadwalController::update/$1');
        $routes->post('jadwal/delete/(:num)', 'JadwalController::delete/$1');
        $routes->post('jadwal/plot-pengawas', 'JadwalController::plotPengawas');
        $routes->post('jadwal/generate-json/(:num)', 'JadwalController::generateJson/$1');
        $routes->post('jadwal/susulan', 'JadwalController::createSusulan');

        $routes->get('jenis-ujian', 'JenisUjianController::index');
        $routes->post('jenis-ujian/store', 'JenisUjianController::store');
        $routes->post('jenis-ujian/update/(:num)', 'JenisUjianController::update/$1');
        $routes->post('jenis-ujian/delete/(:num)', 'JenisUjianController::delete/$1');

        $routes->get('cetak-kartu', 'SiswaController::cetakKartu');
    });

    $routes->group('', ['filter' => 'auth:guru'], static function ($routes) {
        $routes->get('bank-soal', 'GuruController::index');
        $routes->get('bank-soal/create', 'GuruController::create');
        $routes->post('bank-soal/store', 'GuruController::store');
        $routes->get('bank-soal/edit/(:num)', 'GuruController::edit/$1');
        $routes->post('bank-soal/update/(:num)', 'GuruController::update/$1');
        $routes->get('bank-soal/export/(:num)', 'GuruController::export/$1');
        $routes->post('bank-soal/import', 'GuruController::import');
        $routes->post('bank-soal/delete/(:num)', 'GuruController::delete/$1');
        $routes->post('bank-soal/upload-gambar', 'GuruController::uploadGambar');
    });

    $routes->group('ruang-pengawas', static function ($routes) {
        $routes->get('/', 'PengawasController::index');
        $routes->get('monitor/(:num)', 'PengawasController::monitor/$1');
        # $routes->post('generate-token/(:num)', 'PengawasController::generateToken/$1');
        $routes->post('generate-token-ajax/(:num)', 'PengawasController::generateTokenAjax/$1');
        $routes->post('reset-login/(:num)', 'PengawasController::resetLogin/$1');
        $routes->post('force-selesai/(:num)/(:num)', 'PengawasController::forceSelesai/$1/$2');
        $routes->post('tandai-hadir/(:num)/(:num)', 'PengawasController::tandaiHadir/$1/$2');
    });

    $routes->group('penilaian', ['filter' => 'auth:guru'], function ($routes) {
        $routes->get('/', 'PenilaianController::index');
        $routes->get('detail/(:num)', 'PenilaianController::detail/$1');
        $routes->get('export/(:num)', 'PenilaianController::exportExcel/$1');
        $routes->get('koreksi/(:num)/(:num)', 'PenilaianController::koreksi/$1/$2');
        $routes->post('simpan-koreksi', 'PenilaianController::simpanKoreksi');
        $routes->post('susulan-gabungan', 'PenilaianController::createSusulanGabungan');
    });
});
