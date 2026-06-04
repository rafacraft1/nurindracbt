<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class WatermarkFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null) {}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $body = (string)$response->getBody();

        if (stripos($body, '<html') !== false || stripos($body, '<body') !== false) {

            $js = "
            <script>
            (function(){
                const wmId = 'cbt-core-watermark';
                function renderWm() {
                    let el = document.getElementById(wmId);
                    if (!el) {
                        el = document.createElement('div');
                        el.id = wmId;
                        el.innerHTML = '⚡ CBT PRO v2.0 - Protected System';
                        // REVISI: Posisi dipindah ke TENGAH BAWAH agar tidak menutupi sidebar kiri maupun tombol kanan
                        el.setAttribute('style', 'position:fixed;bottom:15px;left:50%;transform:translateX(-50%);z-index:2147483647;font-size:11px;color:#333;background:rgba(255,255,255,0.85);padding:4px 12px;border-radius:6px;pointer-events:none;font-weight:900;letter-spacing:0.5px;box-shadow:0 2px 4px rgba(0,0,0,0.15);backdrop-filter:blur(2px);');
                        if (document.body) document.body.appendChild(el);
                    } else {
                        const css = window.getComputedStyle(el);
                        if (css.display === 'none' || css.visibility === 'hidden' || css.opacity < 0.5) {
                            el.remove();
                            renderWm();
                        }
                    }
                }
                if(document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', renderWm);
                } else {
                    renderWm();
                }
                setInterval(renderWm, 1000);
            })();
            </script>
            ";

            if (stripos($body, '</body>') !== false) {
                $body = str_ireplace('</body>', $js . '</body>', $body);
            } else {
                $body .= $js;
            }

            $response->setBody($body);
        }
    }
}
