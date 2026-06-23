<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ThrottleFilter implements FilterInterface
{
    /**
     * @param IncomingRequest $request
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $throttler = Services::throttler();

        // Limit: 60 Request per Menit per IP Address (Anti-DDoS)
        if ($throttler->check(md5($request->getIPAddress()), 60, MINUTE) === false) {

            // FIX INTELEPHENSE: Menggunakan method konkret atau pengecekan Header XMLHttpRequest secara manual
            $isAjaxRequest = ($request instanceof IncomingRequest && $request->isAJAX())
                || (strtolower($request->header('X-Requested-With')?->getValue() ?? '') === 'xmlhttprequest');

            if ($isAjaxRequest) {
                return Services::response()->setStatusCode(429)->setJSON([
                    'status'  => 'error',
                    'message' => 'Terlalu banyak request (Spam terdeteksi). Harap tunggu 1 menit.'
                ]);
            }

            return Services::response()->setStatusCode(429)->setBody('<h2 style="color:red; text-align:center; margin-top:50px; font-family:sans-serif;">AKSES DIBLOKIR SEMENTARA (DDoS PROTECTION)</h2><p style="text-align:center; font-family:sans-serif;">Terlalu banyak aktivitas terdeteksi dari IP Anda. Silakan tunggu 1 menit sebelum mencoba lagi.</p>');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu logika after
    }
}
