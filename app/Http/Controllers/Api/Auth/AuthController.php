<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Genera una clave única combinando el correo (en minúsculas) y la dirección IP del usuario
        $key = Str::lower($request->email).'|'.$request->ip();

        // Verifica si se ha superado el máximo de intentos permitidos
        if (RateLimiter::tooManyAttempts($key, 4)) {

            // Obtiene la cantidad de segundos restantes para que el usuario pueda volver a intentarlo
            $seconds = RateLimiter::availableIn($key);

            // Retorna una respuesta JSON con código de estado HTTP 429 (Too Many Requests)
            return response()->json([
                'message' => "Demasiados intentos. Intente nuevamente en {$seconds} segundos."
            ], 429);
        }

        // Obtener usuario
        $user = User::where('email', $request->email)->first();

        // verificar credenciales
        if (!$user || !Hash::check($request->password, $user->password)) {

            RateLimiter::hit($key, 60); // bloqueo

            return response()->json([
                'message' => 'Credenciales incorrectas'
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

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => $user,
            // 'token' => $token,  <-- Eliminado porque ahora se viaja seguro por cookie HttpOnly
            'permissions' => $permissions,
            'role' => $role,
            'pnf' => $pnf
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
            'pnf'  => $pnf // Enviamos el PNF en la validación del token
        ]);
    }

    public function logout(Request $request)
    {
        // Logout SPA con cookies HttpOnly (no usa tokens de BD)
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Sesión cerrada correctamente'], 200);
    }
}
