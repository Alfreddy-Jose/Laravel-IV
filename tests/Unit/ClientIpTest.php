<?php

namespace Tests\Unit;

use App\Support\ClientIp;
use Illuminate\Http\Request;
use Tests\TestCase;

class ClientIpTest extends TestCase
{
    public function test_devuelve_la_ip_publica_del_cliente(): void
    {
        $request = Request::create('/', 'GET', server: ['REMOTE_ADDR' => '203.0.113.45']);

        $this->assertSame('203.0.113.45', ClientIp::fromRequest($request));
    }

    public function test_normaliza_la_ipv4_mapeada_en_ipv6(): void
    {
        $request = Request::create('/', 'GET', server: ['REMOTE_ADDR' => '::ffff:192.0.2.10']);

        $this->assertSame('192.0.2.10', ClientIp::fromRequest($request));
    }

    public function test_respeta_x_forwarded_for_cuando_el_proxy_es_confiable(): void
    {
        $request = Request::create('/', 'GET', server: [
            'REMOTE_ADDR' => '10.0.0.5',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.45',
        ]);

        $request->setTrustedProxies(
            [$request->server->get('REMOTE_ADDR')],
            Request::HEADER_X_FORWARDED_FOR
        );

        $this->assertSame('203.0.113.45', ClientIp::fromRequest($request));
    }

    public function test_devuelve_al_cliente_original_en_una_cadena_de_proxies(): void
    {
        // Vercel -> Render: el segundo valor es el proxy, el primero el cliente.
        $request = Request::create('/', 'GET', server: [
            'REMOTE_ADDR' => '10.20.30.40',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.45, 76.76.21.21',
        ]);

        $request->setTrustedProxies(
            [$request->server->get('REMOTE_ADDR')],
            Request::HEADER_X_FORWARDED_FOR
        );

        $this->assertSame('203.0.113.45', ClientIp::fromRequest($request));
    }

    public function test_ignora_x_forwarded_for_si_el_proxy_no_es_confiable(): void
    {
        $request = Request::create('/', 'GET', server: [
            'REMOTE_ADDR' => '198.51.100.7',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.45',
        ]);

        $request->setTrustedProxies([], Request::HEADER_X_FORWARDED_FOR);

        $this->assertSame('198.51.100.7', ClientIp::fromRequest($request));
    }
}
