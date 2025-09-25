<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Os proxies confiáveis para este aplicativo.
     *
     * Use '*' para confiar em todos (reverso proxy da Railway/Cloudflare, etc.).
     */
    protected $proxies = '*';

    /**
     * Os cabeçalhos que devem ser usados para detectar proxies.
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR
                        | Request::HEADER_X_FORWARDED_HOST
                        | Request::HEADER_X_FORWARDED_PORT
                        | Request::HEADER_X_FORWARDED_PROTO
                        | Request::HEADER_X_FORWARDED_AWS_ELB;
}
