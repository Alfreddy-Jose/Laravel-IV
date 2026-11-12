<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Resuelve la dirección IP del cliente de forma uniforme para la auditoría.
 *
 * En despliegues con más de un proxy (por ejemplo Vercel -> Render) el valor
 * de `$request->ip()` puede ser la IP del proxy más cercano y no la del
 * navegador. Cuando la petición viene de un proxy confiable, la cadena
 * `X-Forwarded-For` conserva al cliente original en su primer valor.
 */
class ClientIp
{
    public static function fromRequest(?Request $request = null): ?string
    {
        $request ??= request();

        if (! $request instanceof Request) {
            return null;
        }

        // Solo se confía en la cabecera si Laravel reconoce al proxy de origen
        // (configurado en bootstrap/app.php mediante `trustProxies`).
        if ($request->isFromTrustedProxy()) {
            $forwarded = $request->header('X-Forwarded-For');

            if (is_string($forwarded) && trim($forwarded) !== '') {
                foreach (explode(',', $forwarded) as $candidate) {
                    $candidate = trim($candidate);

                    if (filter_var($candidate, FILTER_VALIDATE_IP)) {
                        return self::normalizar($candidate);
                    }
                }
            }
        }

        return self::normalizar($request->ip());
    }

    private static function normalizar(?string $ip): ?string
    {
        if (! is_string($ip) || trim($ip) === '') {
            return null;
        }

        $ip = trim($ip);

        // IPv4 mapeada en IPv6: "::ffff:192.0.2.10" -> "192.0.2.10".
        if (str_starts_with($ip, '::ffff:')) {
            $mapeada = substr($ip, 7);

            if (filter_var($mapeada, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $mapeada;
            }
        }

        return $ip;
    }
}
