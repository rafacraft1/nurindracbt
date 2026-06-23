<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Security extends BaseConfig
{
    /**
     * CSRF Protection Method
     * Menggunakan session untuk mencegah eksploitasi via pembacaan Cookie
     */
    public string $csrfProtection = 'session';

    /**
     * CSRF Token Randomization
     * FIX CRYPTO: Wajib di-TRUE-kan agar Hacker tidak bisa menebak pola token
     */
    public bool $tokenRandomize = true;

    /**
     * CSRF Token Name
     */
    public string $tokenName = 'csrf_test_name';

    /**
     * CSRF Header Name
     */
    public string $headerName = 'X-CSRF-TOKEN';

    /**
     * CSRF Cookie Name
     */
    public string $cookieName = 'csrf_cookie_name';

    /**
     * CSRF Expires
     */
    public int $expires = 7200;

    /**
     * CSRF Regenerate
     */
    public bool $regenerate = true;

    /**
     * CSRF Redirect
     * Paksa redirect saat request gagal divalidasi
     */
    public bool $redirect = true;
}
