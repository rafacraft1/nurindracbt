<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userType  = $session->get('user_type');
        $role      = $session->get('role');
        $isPanitia = $session->get('is_panitia');
        $uri       = uri_string();

        if ($userType === 'siswa') {
            if (strpos($uri, 'panel') === 0) {
                return redirect()->to('/ujian');
            }
            return;
        }

        if ($userType === 'staff') {
            if (strpos($uri, 'ujian') === 0) {
                return redirect()->to('/panel/dashboard');
            }

            if ($role === 'admin') {
                return;
            }

            if ($arguments) {
                $roleRequired = $arguments[0];

                if ($roleRequired === 'panitia' && $isPanitia != 1) {
                    return redirect()->to('/panel/dashboard')->with('error', 'Akses ditolak! Khusus Panitia.');
                }
                if ($roleRequired === 'guru' && $role !== 'guru') {
                    return redirect()->to('/panel/dashboard')->with('error', 'Akses ditolak! Khusus Guru.');
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
