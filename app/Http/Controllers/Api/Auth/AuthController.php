<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\User;
use App\Support\ClientIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Genera una clave única combinando el correo (en minúsculas) y la dirección IP del usuario
        $key = Str::lower($request->email).'|'.ClientIp::fromRequest($request);

        // Verifica si se ha superado el máximo de intentos permitidos
        if (RateLimiter::tooManyAttempts($key, 4)) {

            // Obtiene la cantidad de segundos restantes para que el usuario pueda volver a intentarlo
            $seconds = RateLimiter::availableIn($key);

            // Auditoría del intento fallido (nunca bloquea el flujo)
            $this->registrarEventoSesion(null, 'INTENTO_FALLIDO', [
                'email' => $request->email,
                'motivo' => 'Demasiados intentos',
            ]);

            // Retorna una respuesta JSON con código de estado HTTP 429 (Too Many Requests)
            return response()->json([
                'message' => "Demasiados intentos. Intente nuevamente en {$seconds} segundos.",
            ], 429);
        }

        // Obtener usuario
        $user = User::where('email', $request->email)->first();

        // verificar credenciales
        if (! $user || ! Hash::check($request->password, $user->password)) {

            RateLimiter::hit($key, 60); // bloqueo

            // Auditoría del intento fallido (nunca bloquea el flujo)
            $this->registrarEventoSesion($user, 'INTENTO_FALLIDO', [
                'email' => $request->email,
                'motivo' => 'Credenciales incorrectas',
            ]);

            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        RateLimiter::clear($key);

        // CAMBIO: En lugar de crear un token plano, iniciamos sesión para generar la cookie HttpOnly
        Auth::login($user);
        $request->session()->regenerate();

        // Obtener nombres de los permisos (¡Intacto tal como lo tenías!)
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();

        // Obtener Rol del usuario
        $role = $user->roles->pluck('name')->first();

        // Definiendo varible PNF
        $pnf = null;

        // Si el rol del usuario es COORDINADOR buscar el Pnf del coordinador
        if ($user->hasRole('COORDINADOR') && $user->persona && $user->persona->docente) {
            $pnf = $user->persona->docente->pnf_id;
        }

        \Log::info('Sesión antes de CSRF:', ['session_id' => $request->session()->getId()]);

        // Auditoría del inicio de sesión exitoso (nunca bloquea el flujo)
        $this->registrarEventoSesion($user, 'INICIO_SESION', [
            'email' => $user->email,
            'nombre' => $user->name,
            'rol' => $role,
        ]);

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => $user,
            // 'token' => $token,  <-- Eliminado porque ahora se viaja seguro por cookie HttpOnly
            'permissions' => $permissions,
            'role' => $role,
            'pnf' => $pnf,
        ], 200);
    }

    // Obtener el usuario autenticado (para recargas de página / persistencia)
    public function user(Request $request)
    {
        $user = $request->user()->load(['roles']);

        // Aplicamos exactamente la misma lógica que en el método Login:
        $pnf = null;

        if ($user->hasRole('COORDINADOR') && $user->persona && $user->persona->docente) {
            $pnf = $user->persona->docente->pnf_id;
        }

        return response()->json([
            'user' => $user,
            'pnf' => $pnf, // Enviamos el PNF en la validación del token
        ]);
    }

    public function logout(Request $request)
    {
        // Usuario autenticado antes de invalidar la sesión
        $user = $request->user();

        // Auditoría del cierre de sesión (nunca bloquea el flujo)
        $this->registrarEventoSesion($user, 'CIERRE_SESION', [
            'email' => $user?->email,
        ]);

        // Logout SPA con cookies HttpOnly (no usa tokens de BD)
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Sesión cerrada correctamente'], 200);
    }

    /**
     * Registra un evento de sesión en la bitácora.
     *
     * Se ejecuta de forma defensiva: cualquier fallo de auditoría se registra
     * en el log pero nunca interrumpe el flujo de login/logout.
     */
    private function registrarEventoSesion(?User $user, string $accion, array $detalles = []): void
    {
        try {
            Bitacora::create([
                'user_id' => $user?->id,
                'accion' => $accion,
                'tabla' => 'sesiones',
                'registro_id' => $user?->id,
                'datos_anteriores' => null,
                'datos_nuevos' => $detalles ?: null,
                'ip_address' => ClientIp::fromRequest(),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('No se pudo registrar el evento de sesión '.$accion, [
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
