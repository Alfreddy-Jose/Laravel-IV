<?php

namespace App\Observers;
use App\Models\Coordinador;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Notifications\WelcomeUserNotification;
use App\Notifications\UsuarioCreadoParaCoordinador;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CoordinadorObserver
{
    /**
     * Cuando se crea un coordinador → crear usuario.
     */
    public function created(Coordinador $coordinador): void
    {
        $docente = $coordinador->docente;
        $persona = $docente->persona;

        // Evitar duplicados
        if (User::where('persona_id', $persona->id)->exists()) {
            return;
        }

        $user = User::create([
            'name'     => $persona->nombre . ' ' . $persona->apellido,
            'email'    => $persona->email,
            'password' => Hash::make($persona->cedula_persona),
            'persona_id' => $persona->id,
        ]);

        $user->assignRole('COORDINADOR');

        // Enviamos la notificación usando la clase WelcomeUserNotification
        $user->notify(new WelcomeUserNotification($user, $persona->cedula_persona));

        if (auth()->check()) {
            $administrador = auth()->user();
            // Guardar notificacion de creacion de usuario para coordinador craedo
            $administrador->notify(new UsuarioCreadoParaCoordinador($coordinador, $user));
        }
    }

    /**
     * ESTE ES EL IMPORTANTE — siempre corre aunque uses transacciones.
     */
    public function deleting(Coordinador $coordinador): void
    {
    // Forzamos a Laravel a cargar las relaciones frescas de la base de datos
        $coordinador->loadMissing('docente.persona');

        $docente = $coordinador->docente;
        if (!$docente) {
            // Si entra aquí, el coordinador ya no tiene docente vinculado en la BD
            return;
        }

        $persona = $docente->persona;
        if (!$persona) {
            return;
        }

        // Buscamos al usuario usando el ID de la persona
        $usuario = User::where('persona_id', $persona->id)->first();

        if ($usuario) {
            $usuario->delete();
        }
    }
}
