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

    $routes->group('', ['filter' => 'auth:panitia'], static function ($routes) {

        $routes->get('ruangan', 'PanitiaController::ruangan');
        $routes->post('ruangan/store', 'PanitiaController::storeRuangan');
        $routes->post('ruangan/delete/(:num)', 'PanitiaController::deleteRuangan/$1');
        $routes->post('ruangan/plot-siswa', 'PanitiaController::plotSiswaRuangan');
        $routes->post('ruangan/kosongkan/(:num)', 'PanitiaController::kosongkanRuangan/$1');

        $routes->get('siswa', 'PanitiaController::siswa');
        $routes->post('siswa/store', 'PanitiaController::storeSiswa');
        $routes->post('siswa/update/(:num)', 'PanitiaController::updateSiswa/$1');
        $routes->post('siswa/delete/(:num)', 'PanitiaController::deleteSiswa/$1');
        $routes->post('siswa/import', 'PanitiaController::importSiswa');

        $routes->get('mapel', 'PanitiaController::mapel');
        $routes->post('mapel/store', 'PanitiaController::storeMapel');
        $routes->post('mapel/delete/(:num)', 'PanitiaController::deleteMapel/$1');
        $routes->post('mapel/sync-guru', 'PanitiaController::syncGuruMapel');

        $routes->get('jadwal', 'PanitiaController::jadwal');
        $routes->post('jadwal/store', 'PanitiaController::storeJadwal');
        $routes->post('jadwal/update/(:num)', 'PanitiaController::updateJadwal/$1');
        $routes->post('jadwal/delete/(:num)', 'PanitiaController::deleteJadwal/$1');
        $routes->post('jadwal/plot-pengawas', 'PanitiaController::plotPengawas');
        $routes->post('jadwal/generate-json/(:num)', 'PanitiaController::generateJson/$1');

        $routes->get('jenis-ujian', 'PanitiaController::jenisUjian');
        $routes->post('jenis-ujian/store', 'PanitiaController::storeJenisUjian');
        $routes->post('jenis-ujian/update/(:num)', 'PanitiaController::updateJenisUjian/$1');
        $routes->post('jenis-ujian/delete/(:num)', 'PanitiaController::deleteJenisUjian/$1');

        $routes->get('cetak-kartu', 'PanitiaController::cetakKartu');
    });

    $routes->group('', ['filter' => 'auth:guru'], static function ($routes) {
        $routes->get('bank-soal', 'GuruController::bankSoal');
        $routes->get('bank-soal/create', 'GuruController::createSoal');
        $routes->post('bank-soal/store', 'GuruController::storeSoal');
        $routes->get('bank-soal/edit/(:num)', 'GuruController::editSoal/$1');
        $routes->post('bank-soal/update/(:num)', 'GuruController::updateSoal/$1');
        $routes->get('bank-soal/export/(:num)', 'GuruController::exportSoal/$1');
        $routes->post('bank-soal/import', 'GuruController::importSoal');
        $routes->post('bank-soal/delete/(:num)', 'GuruController::deleteSoal/$1');
    });

    $routes->group('ruang-pengawas', static function ($routes) {
        $routes->get('/', 'PengawasController::index');
        $routes->get('monitor/(:num)', 'PengawasController::monitor/$1');
        $routes->post('generate-token/(:num)', 'PengawasController::generateToken/$1');
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
    });
});
